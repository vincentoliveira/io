<?php

namespace IO\OrderBundle\Service\StatsCalculator;

use Doctrine\ORM\EntityManager;

/**
 * TimeDistribution Calculator
 */
class TimeDistributionCalculator implements StatsCalculatorInterface {
 
    /**
     * @{inheritDoc}
     */
    public function calculate(EntityManager $em, $filters = array()) {
        if (!isset($filters['restaurant_id'])) {
            return array();
        }
        
        if (isset($filters['step'])) {
            $step = $filters['step'];
        } else {
            $step = 600;
        }
        
        $repo = $em->getRepository('IOOrderBundle:OrderData');
        $qb = $repo->createQueryBuilder('orderItem')
                ->select('orderItem.orderDate')
                ->where('orderItem.restaurant = :restaurant')
                ->setParameter(':restaurant', $filters['restaurant_id']);
        
        $orderTimeResult = $qb->getQuery()->getArrayResult();
        
        $timeResult = array();
        foreach ($orderTimeResult as $order) {
            $order['orderDate']->setTimezone(new \DateTimeZone('Europe/Paris'));
            $time = intval((($order['orderDate']->getTimestamp() + 2 * 3600) % (3600 * 24)) / $step);
            if (!isset($timeResult[$time])) {
                $timeResult[$time] = 1;
            } else {
                $timeResult[$time]++;
            }
        }
        
        $keys = array_keys($timeResult);
        $min = min($keys);
        $max = max($keys);
        
        $result = array();
        for ($t = $min; $t <= $max; $t++) {
            $timeStr = intval(($t * $step) / 3600) . 'h' . intval(($t * $step / 60) % 60);
            $nbAtThisTime = isset($timeResult[$t]) ? $timeResult[$t] : 0;
            $result[] = array($timeStr, $nbAtThisTime);
        }
        
        return $result;
    }

}
