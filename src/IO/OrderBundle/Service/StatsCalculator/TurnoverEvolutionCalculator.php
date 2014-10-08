<?php

namespace IO\OrderBundle\Service\StatsCalculator;

use Doctrine\ORM\EntityManager;

/**
 * TurnoverEvolution Calculator
 */
class TurnoverEvolutionCalculator implements StatsCalculatorInterface
{

    /**
     * @{inheritDoc}
     */
    public function calculate(EntityManager $em, $filters = array())
    {
        if (!isset($filters['restaurant_id'])) {
            return array();
        }

        $start = $filters['start_date'];
        $end = $filters['end_date'];

        $repo = $em->getRepository('IOOrderBundle:OrderData');
        $qb = $repo->createQueryBuilder('orderData')
                ->select('orderData.orderDate', 'SUM(payments.amount)')
                ->leftJoin(('orderData.orderPayments'), 'payments')
                ->where('orderData.restaurant = :restaurant')
                ->andWhere('orderData.orderDate BETWEEN :dateStart and :dateEnd')
                ->groupBy('orderData.orderDate')
                ->orderBy('orderData.orderDate', 'ASC')
                ->setParameter(':restaurant', $filters['restaurant_id'])
                ->setParameter(':dateStart', $start)
                ->setParameter(':dateEnd', $end);

        $countByDates = $qb->getQuery()->getArrayResult();

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($start, $interval, $end);

        $result = array();
        foreach ($period as $dt) {
            $result[$dt->format('d-m-Y')] = [$dt->format('d-m-Y'), 0];
        }
        
        foreach ($countByDates as $coutByDate) {
            if (isset($coutByDate[1]) && isset($result[$coutByDate['orderDate']->format('d-m-Y')])) {
                $result[$coutByDate['orderDate']->format('d-m-Y')][1] = floatval($coutByDate[1]);
            }
        }
        
        return $result;
    }

}
