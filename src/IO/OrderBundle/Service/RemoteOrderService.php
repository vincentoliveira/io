<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Entity\OrderData;
use IO\OrderBundle\Entity\OrderLine;
use IO\OrderBundle\Entity\OrderStatus;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\OrderBundle\Enum\OrderStatusEnum;
use IO\OrderBundle\Entity\Customer;

/**
 * Remote Order Service
 * 
 * @Service("io.remote_order_service")
 */
class RemoteOrderService {

    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * Session
     * 
     * @Inject("session")
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    public $session;

    /**
     * Get current draft order
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function getCurrentDraftOrder(Restaurant $restaurant) {
        $draftId = $this->session->get("draft_id", 0);

        $repo = $this->em->getRepository('IOOrderBundle:OrderData');
        $draftOrder = $repo->find($draftId);

        if ($draftOrder === null || $draftOrder->getRestaurant() !== $restaurant || 
                $draftOrder->getLastStatus() !== OrderStatusEnum::STATUS_DRAFT) {
            $draftOrder = new OrderData();
            $draftOrder->setRestaurant($restaurant);
        }

        return $draftOrder;
    }

    /**
     * Store current draft ordern 
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function setCurrentDraftOrder(OrderData $draftOrder) {
        $this->session->set("draft_id", $draftOrder->getId());
        $this->session->save();
    }

    /**
     * Add product to draft order
     * 
     * @param type $restaurant
     * @param \IO\OrderBundle\Service\CarteItem $product
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function addProductToOrder($restaurant, CarteItem $product) {
        $draftOrder = $this->getCurrentDraftOrder($restaurant);

        $orderLine = new OrderLine();
        $orderLine->setOrder($draftOrder);
        $orderLine->setItem($product);
        $orderLine->setItemShortName($product->getShortName());
        $orderLine->setItemPrice($product->getPrice());
        $orderLine->setItemVat($product->getVat());
        $this->em->persist($orderLine);

        $draftOrder->addOrderLine($orderLine);

        if ($draftOrder->getLastStatus() === OrderStatusEnum::STATUS_INIT) {
            $status = new OrderStatus();
            $status->setOrder($draftOrder);
            $status->setDate(new \DateTime());
            $status->setOldStatus(OrderStatusEnum::STATUS_INIT);
            $status->setNewStatus(OrderStatusEnum::STATUS_DRAFT);
            $this->em->persist($status);

            $draftOrder->addOrderStatus($status);
            $draftOrder->setOrderDate(new \DateTime());

            if ($draftOrder->getStartDate() === null) {
                $draftOrder->setStartDate(new \DateTime());
            }
        }

        $this->em->persist($draftOrder);
        $this->em->flush();

        return $draftOrder;
    }

    /**
     * Send Order
     * 
     * @param \IO\OrderBundle\Entity\OrderData $draftOrder
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function sendOrder(OrderData $draftOrder) {
        
        $status = new OrderStatus();
        $status->setOrder($draftOrder);
        $status->setDate(new \DateTime());
        $status->setOldStatus($draftOrder->getLastStatus());
        $status->setNewStatus(OrderStatusEnum::STATUS_WAITING);
        $this->em->persist($status);

        $draftOrder->addOrderStatus($status);
        
        $this->em->persist($draftOrder);
        $this->em->flush();
        
        //@TODO: send email

        return $draftOrder;
    }

}
