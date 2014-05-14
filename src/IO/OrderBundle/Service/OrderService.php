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
    public function processOrder(array $data, Restaurant $restaurant)
    {
        $order = new Order();
        
        $order->setRestaurant($restaurant);
        $order->setOrderDate(new \DateTime());
        $order->setStatus(OrderStatusEnum::STATUS_WAITING);
        
        $repo = $this->em->getRepository('IORestaurantBundle:CarteItem');
        foreach ($data as $itemData) {
            $item = $repo->find($itemData['id']);
            if ($item !== null || $item->getRestaurant() !== $restaurant) {
                $orderLine = new OrderLine();
                $orderLine->setItem($item);
                $orderLine->setItemPrice($item->getPrice());
                $orderLine->setItemVat($item->getVat());
                $orderLine->setItemShortName($item->getName());
                $this->em->persist($orderLine);
                
                $order->addOrderLine($orderLine);
            }
        }
        
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }
}
