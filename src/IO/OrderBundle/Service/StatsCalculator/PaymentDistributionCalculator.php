<?php

namespace IO\OrderBundle\Service\StatsCalculator;

use Doctrine\ORM\EntityManager;

/**
 * PaymentDistribution Calculator
 */
class PaymentDistributionCalculator implements StatsCalculatorInterface {
 
    /**
     * @{inheritDoc}
     */
    public function calculate(EntityManager $em, $filters = array()) {
        if (!isset($filters['restaurant_id'])) {
            return array();
        }
        
        $repo = $em->getRepository('IOOrderBundle:OrderPayment');
        $qb = $repo->createQueryBuilder('payment')
                ->select('payment.type, count(payment.id)')
                ->leftJoin('payment.order', 'orderData')
                ->where('orderData.restaurant = :restaurant')
                ->setParameter(':restaurant', $filters['restaurant_id'])
                ->groupBy('payment.type');
        
        $queryResult = $qb->getQuery()->getResult();
        
        $results = array();
        foreach ($queryResult as $data) {
            $values = array_values($data);
            $values[1] = intval($values[1]);
            $results[] = $values;
        }
        
        return $results;
    }
    
    /**
     * @{inheritDoc}
     */
    public function calculateAmount(EntityManager $em, $filters = array()) {
        if (!isset($filters['restaurant_id'])) {
            return array();
        }
        
        $repo = $em->getRepository('IOOrderBundle:OrderPayment');
        $qb = $repo->createQueryBuilder('payment')
                ->select('payment.type, sum(payment.amount)')
                ->leftJoin('payment.order', 'orderData')
                ->where('orderData.restaurant = :restaurant')
                ->setParameter(':restaurant', $filters['restaurant_id'])
                ->groupBy('payment.type');
        
        $queryResult = $qb->getQuery()->getResult();
        
        $results = array();
        foreach ($queryResult as $data) {
            $values = array_values($data);
            $values[1] = intval($values[1]);
            $results[] = $values;
        }
        
        return $results;
    }

}
