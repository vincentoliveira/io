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

        $order->addOrderStatuse($status);

        if (isset($data['name'])) {
            $order->setTableName($data['name']);
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
                $orderLine->setItemVat($item->getVat());
                $orderLine->setItemShortName($item->getShortName());
                $orderLine->setOrder($order);
                
                if (isset($itemData['extra'])) {
                    $orderLine->setExtra($itemData['extra']);
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
            if (isset($line->getExtra())) {
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
                $receipt[$parent][$name]['count']++;
            }
        }

        return $receipt;
    }


}
