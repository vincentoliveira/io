<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Entity\OrderData;
use IO\OrderBundle\Entity\OrderLine;
use IO\OrderBundle\Entity\OrderStatus;
use IO\OrderBundle\Entity\OrderPayment;
use IO\OrderBundle\Enum\OrderStatusEnum;
use IO\OrderBundle\Enum\PaymentTypeEnum;
use IO\OrderBundle\Enum\PaymentStatusEnum;
use IO\OrderBundle\Entity\Customer;
use IO\ApiBundle\Entity\AuthToken;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

/**
 * Order Service
 * 
 * @Service("io.order_service")
 */
class OrderService
{

    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * Is order data locked
     * 
     * @param \IO\OrderBundle\Entity\OrderData $order
     * @return boolean
     */
    public function isLocked(OrderData $order)
    {
        $lockStatus = array(
            OrderStatusEnum::STATUS_IN_PROGRESS,
            OrderStatusEnum::STATUS_READY,
            OrderStatusEnum::STATUS_CANCELED,
            OrderStatusEnum::STATUS_CLOSED,
        );
        return in_array($order->getLastStatus(), $lockStatus);
    }

    /**
     * Create an order
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param \IO\ApiBundle\Entity\AuthToken $token
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function createOrder(Restaurant $restaurant, AuthToken $token = null)
    {
        $cart = new OrderData();
        $cart->setRestaurant($restaurant);
        $cart->setToken($token);
        $cart->setStartDate(new \DateTime());

        $status = new OrderStatus();
        $status->setOrder($cart);
        $status->setDate(new \DateTime());
        $status->setOldStatus(OrderStatusEnum::STATUS_INIT);
        $status->setNewStatus(OrderStatusEnum::STATUS_DRAFT);

        $cart->addOrderStatus($status);

        $this->em->persist($status);
        $this->em->persist($cart);

        $this->em->flush();

        return $cart;
    }

    /**
     * Add product from its id to an existing order
     * 
     * @param \IO\OrderBundle\Entity\OrderData $order
     * @param integer $productId
     * @param array $options
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function addProductToOrder(OrderData $order, $productId, array $options = null)
    {
        $repo = $this->em->getRepository('IORestaurantBundle:CarteItem');
        $product = $repo->find($productId);
        if ($product === null ||
                $product->getItemType() !== ItemTypeEnum::TYPE_DISH ||
                $product->getRestaurant() !== $order->getRestaurant()) {
            return $order;
        }

        $orderLine = new OrderLine();
        $orderLine->setItem($product);
        $orderLine->setItemPrice($product->getPrice());
        if ($product->getVat()) {
            $orderLine->setItemVat($product->getVat()->getValue());
        } else {
            $orderLine->setItemVat(20);
        }
        $orderLine->setItemName($product->getName());
        $orderLine->setItemShortName($product->getShortName());
        $orderLine->setOrder($order);

        if ($options !== null) {
            foreach ($options as $optionId) {
                $orderLine = $this->addOptionToOrderLine($orderLine, $optionId);
            }
        }

        $order->addOrderLine($orderLine);

        $this->em->persist($orderLine);
        $this->em->persist($order);

        $this->em->flush();

        return $order;
    }

    /**
     * Add option to order line
     * 
     * @param \IO\OrderBundle\Entity\OrderLine $orderLine
     * @param integer $optionId
     * @return \IO\OrderBundle\Entity\OrderLine
     */
    protected function addOptionToOrderLine(OrderLine $orderLine, $optionId)
    {
        $product = $orderLine->getItem();

        $repo = $this->em->getRepository('IORestaurantBundle:CarteItem');
        $option = $repo->find($optionId);
        if ($option !== null &&
                $option->getItemType() === ItemTypeEnum::TYPE_OPTION_CHOICE &&
                $product->getDishOptions()->contains($option->getParent())) {

            $extra = $orderLine->getExtra();
            if ($extra === null || $extra === '') {
                $extra = $option->getShortName();
            } else {
                $extra = $extra . ' - ' . $option->getShortName();
            }

            $price = $orderLine->getItemPrice() + $option->getPrice();

            $orderLine->setExtra($extra);
            $orderLine->setItemPrice($price);
        }

        return $orderLine;
    }
    

    /**
     * Remove a product from its id from an existing order
     * 
     * @param \IO\OrderBundle\Entity\OrderData $order
     * @param integer $productId
     * @param array $options
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function removeProductFromOrder(OrderData $order, $productId, $extra = null)
    {
        foreach ($order->getOrderLines() as $orderLine) {
            $product = $orderLine->getItem();
            if ($product->getId() === $productId && $orderLine->getExtra() === $extra) {
                $order->removeOrderLine($orderLine);
                $this->em->remove($orderLine);
                $this->em->persist($order);
                $this->em->flush();
                break;
            }
        }
        
        return $order;
    }
    
    /**
     * Validate cart :
     *  - Set status visible for the ePOS
     *  - A client
     *  - Add delivery datetime
     * 
     * @param \IO\OrderBundle\Entity\OrderData $order
     * @param \IO\ApiBundle\Entity\AuthToken $userToken
     * @param string $deliveryDateParam
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function validateCart(OrderData $order, AuthToken $userToken, $deliveryDateParam)
    {
        $deliveryDate = null;
        if ($deliveryDateParam) {
            $deliveryDate = \DateTime::createFromFormat('Y-m-d H:i:s', $deliveryDateParam);
        }
        if (!$deliveryDate) {
            $deliveryDate = new \DateTime();
        }
        
        $orderStatus = new OrderStatus();
        $orderStatus->setOrder($order);
        $orderStatus->setDate(new \DateTime());
        $orderStatus->setOldStatus($order->getLastStatus());
        $orderStatus->setNewStatus(OrderStatusEnum::STATUS_INIT);
        $this->em->persist($orderStatus);
        
        $order->setOrderDate($deliveryDate);
        $order->addOrderStatus($orderStatus);
        $order->setClient($userToken->getUser());
        
        $this->em->persist($order);
        $this->em->flush();
        
        return $order;
    }

    /**
     * process order from data
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function getCurrentOrders(Restaurant $restaurant)
    {
        $repo = $this->em->getRepository('IOOrderBundle:OrderData');
        $qb = $repo->createQueryBuilder('order_data');
        $qb->select('order_data')
                ->leftJoin('order_data.orderStatuses', 'order_status')
                ->where('order_data.restaurant = :restaurant')
                ->groupBy('order_data.id')
                ->having('GROUP_CONCAT(order_status.newStatus) NOT LIKE :status_closed')
                ->andHaving('GROUP_CONCAT(order_status.newStatus) NOT LIKE :status_canceled')
                ->setParameter(':restaurant', $restaurant)
                ->setParameter(':status_closed', '%' . OrderStatusEnum::STATUS_CLOSED . '%')
                ->setParameter(':status_canceled', '%' . OrderStatusEnum::STATUS_CANCELED . '%');

        $orders = $qb->getQuery()->getResult();

        return $orders;
    }

    /**
     * process order from data
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function processOrder(array $data, Restaurant $restaurant)
    {
        $order = new OrderData();

        $order->setRestaurant($restaurant);
        $order->setOrderDate(new \DateTime());

        $status = new OrderStatus();
        $status->setOrder($order);
        $status->setDate(new \DateTime());
        $status->setOldStatus(OrderStatusEnum::STATUS_INIT);
        $status->setNewStatus(OrderStatusEnum::STATUS_WAITING);
        $this->em->persist($status);

        $order->addOrderStatus($status);

        if (isset($data['name'])) {
            $name = $data['name'];
            $customer = $this->em->getRepository('IOOrderBundle:Customer')->findOneByName($name);

            if ($customer === null) {
                $customer = new Customer();
                $customer->setName($name);
            }

//            $order->setCustomer($customer);
        }

        $date = null;
        if (isset($data['start_date'])) {
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data['start_date']);
        }
        if (!$date instanceof \DateTime) {
            $date = new \DateTime();
        }
        $order->setStartDate($date);

        $repo = $this->em->getRepository('IORestaurantBundle:CarteItem');
        foreach ($data['items'] as $itemData) {
            $item = $repo->find($itemData['id']);
            if ($item !== null || $item->getRestaurant() !== $restaurant) {
                $orderLine = new OrderLine();
                $orderLine->setItem($item);
                $orderLine->setItemPrice($item->getPrice());
                $orderLine->setItemVat($item->getVat()->getValue());
                $orderLine->setItemShortName($item->getShortName());
                $orderLine->setOrder($order);

                if (isset($itemData['extra'])) {
                    $orderLine->setExtra($itemData['extra']);
                }
                if (isset($itemData['price'])) {
                    $orderLine->setItemPrice(floatval($itemData['price']));
                }

                $this->em->persist($orderLine);

                $order->addOrderLine($orderLine);
            }
        }

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * process payment from data
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function processPayment(OrderData $order, array $data)
    {
        $date = isset($data['date']) ? \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']) : null;
        if (!$date) {
            $date = new \DateTime();
        }

        $comments = isset($data['comments']) ? base64_decode($data['comments']) : '';
        $amount = isset($data['amount']) ? floatval($data['amount']) : null;
        $transactionId = isset($data['transaction_id']) ? $data['transaction_id'] : null;

        if ($amount < 0) {
            $status = PaymentStatusEnum::PAYMENT_ERROR;
            $comments .= "NO AMOUNT;";
        } elseif (isset($data['status']) && in_array($data['status'], PaymentStatusEnum::$allowedStatuses)) {
            $status = $data['status'];
        } else {
            $status = PaymentStatusEnum::PAYMENT_ERROR;
        }

        if (isset($data['type']) && in_array($data['type'], PaymentTypeEnum::$allowedType)) {
            $type = $data['type'];
        } else {
            $status = PaymentStatusEnum::PAYMENT_ERROR;
            $type = PaymentTypeEnum::PAYMENT_UNKNOWN;
            $comments .= "NO PAYMENT TYPE;";
        }


        $payment = new OrderPayment();
        $payment->setOrder($order);
        $payment->setAmount($amount);
        $payment->setDate($date);
        $payment->setType($type);
        $payment->setTransactionId($transactionId);
        $payment->setStatus($status);
        if ($comments !== '') {
            $payment->setComments(base64_encode($comments));
        }

        $this->em->persist($payment);
        $this->em->flush();
    }

    /**
     * Generate Receipt
     * 
     * @param \IO\OrderBundle\Entity\OrderData $order
     * @return type
     */
    public function generateReceipt(OrderData $order)
    {
        $receipt = array();

        foreach ($order->getOrderLines() as $line) {
            $name = strtoupper($line->getItemShortName());
            if ($line->getExtra() !== null) {
                $name .= strtoupper($line->getExtra());
            }
            if ($line->getItem() !== null && $line->getItem()->getParent() !== null) {
                $parent = strtoupper($line->getItem()->getParent()->getShortName());
            } else {
                $parent = '-';
            }

            if (!isset($receipt[$parent])) {
                $receipt[$parent] = array();
            }

            if (!isset($receipt[$parent][$name])) {
                $receipt[$parent][$name] = array(
                    'count' => 1,
                    'item' => $line,
                );
            } else {
                $receipt[$parent][$name]['count'] ++;
            }
        }

        return $receipt;
    }

}
