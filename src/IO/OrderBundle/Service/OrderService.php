<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Entity\Order;
use IO\OrderBundle\Entity\OrderLine;
use IO\OrderBundle\Enum\OrderStatusEnum;

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
     * @return \IO\OrderBundle\Entity\Order
     */
    public function getCurrentOrders(Restaurant $restaurant)
    {
        $repo = $this->em->getRepository('IOOrderBundle:Order');
        $orders = $repo->findBy(array(
            'restaurant' => $restaurant,
            'status' => array(OrderStatusEnum::STATUS_WAITING, OrderStatusEnum::STATUS_IN_PROGRESS),
        ));
                
        return $orders;
    }
    
    /**
     * process order from data
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\Order
     */
    public function processOrder(array $data, Restaurant $restaurant)
    {
        $order = new Order();
        
        $order->setRestaurant($restaurant);
        $order->setOrderDate(new \DateTime());
        $order->setStatus(OrderStatusEnum::STATUS_WAITING);
        
        if (isset($data['name'])) {
            $order->setTableName($data['name']);
        }
        
        if (isset($data['start_date'])) {
            $order->setStartDate(\DateTime::createFromFormat('Y-m-d H:i:s', $data['start_date']));
        }
        
        $repo = $this->em->getRepository('IORestaurantBundle:CarteItem');
        foreach ($data['items'] as $itemData) {
            $item = $repo->find($itemData['id']);
            if ($item !== null || $item->getRestaurant() !== $restaurant) {
                $orderLine = new OrderLine();
                $orderLine->setItem($item);
                $orderLine->setItemPrice($item->getPrice());
                $orderLine->setItemVat($item->getVat());
                $orderLine->setItemShortName($item->getName());
                $orderLine->setOrder($order);
                $this->em->persist($orderLine);
            }
        }
        
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }
    
    /**
     * Generate Receipt
     * 
     * @param \IO\OrderBundle\Entity\Order $order
     * @return type
     */
    public function generateReceipt(Order $order) 
    {
        $receipt = array();
        
        foreach ($order->getOrderLines() as $line) {
            $name = strtoupper($line->getItemShortName());
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
