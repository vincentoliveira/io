<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Service\QueryBuilder\MySQLQueryBuilder;

/**
 * History Service
 * 
 * @Service("io.payment_history_service")
 */
class PaymentHistoryService {

    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;
 
    /**
     * Get payment for restaurant
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param array $filters
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function getPayments(Restaurant $restaurant, array $filters = array())
    {
        $repo = $this->em->getRepository('IOOrderBundle:OrderPayment');
        $qb = $repo->createQueryBuilder('order_payment');

        $count = isset($filters['count']) ? intval($filters['count']) : 50;
        $page = isset($filters['page']) ? intval($filters['page']) : 1;
        
        $qb->select('order_payment')
                ->leftJoin('order_payment.order', 'order_data')
                ->where('order_data.restaurant = :restaurant')
                ->setParameter(':restaurant', $restaurant)
                ->setFirstResult($count * ($page - 1))
                ->setMaxResults($count);
        
        $payements = $qb->getQuery()->getResult();

        return $payements;
    }
}
