<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Service\QueryBuilder\MySQLQueryBuilder;

/**
 * History Service
 * 
 * @Service("io.history_service")
 */
class HistoryService {

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
    public function getDayHistory(\DateTime $date, Restaurant $restaurant) {
        $repo = $this->em->getRepository('IOOrderBundle:OrderData');
        $qb = $repo->createQueryBuilder('order_item');
        $qb->where('order_item.restaurant = :restaurant')
                ->andWhere('DAY(order_item.orderDate) = :day')
                ->andWhere('MONTH(order_item.orderDate) = :month')
                ->andWhere('YEAR(order_item.orderDate) = :year')
                ->setParameter('restaurant', $restaurant)
                ->setParameter('day', $date->format('d'))
                ->setParameter('month', $date->format('m'))
                ->setParameter('year', $date->format('Y'));
              
        $orders = $qb->getQuery()->getResult();
        return $orders;
    }

    /**
     * Get order hisoty per day
     * 
     * @TODO: Dont calculate canceled orders
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function getOrderHistoryPerDay(Restaurant $restaurant, array $filters = array(), $maxResults = 20, $firstResult = 0) {
        $metadata = $this->em->getClassMetadata('IOOrderBundle:OrderData');
        $metadataOL = $this->em->getClassMetadata('IOOrderBundle:OrderLine');
        $metadataPayment = $this->em->getClassMetadata('IOOrderBundle:OrderPayment');
//        $metadataStatus = $this->em->getClassMetadata('IOOrderBundle:OrderStatus');

        $tableName = $metadata->getTableName();
        $orderLineTableName = $metadataOL->getTableName();
        $orderPaymentTableName = $metadataPayment->getTableName();
//        $orderStatusTableName = $metadataStatus->getTableName();
        
        $qb = new MySQLQueryBuilder();
        
        $sqlQuery = $qb->select(array(
            sprintf('DATE(%s.%s)', $tableName, $metadata->getColumnName('orderDate')) => 'history_date',
            sprintf('COUNT(DISTINCT %s.%s)', $tableName, $metadata->getColumnName('id')) => 'count',
            sprintf('SUM(%s.%s)', $orderLineTableName, $metadataOL->getColumnName('itemPrice')) => 'total',
            sprintf('SUM(%s.%s * %s.%s / 100)', $orderLineTableName, $metadataOL->getColumnName('itemPrice'), $orderLineTableName, $metadataOL->getColumnName('itemVat')) => 'total_vat',
            sprintf('COUNT(DISTINCT %s.%s)', $orderPaymentTableName, $metadataPayment->getSingleAssociationJoinColumnName('order')) => 'count_payed',
            sprintf('SUM(%s.%s)', $orderPaymentTableName, $metadataPayment->getColumnName('amount')) => 'total_payed',
//            sprintf('GROUP_CONCAT(%s.%s)', $orderStatusTableName, $metadataStatus->getColumnName('newStatus')) => 'status',
        ));
        $sqlQuery .= $qb->from($tableName);
        
        // join order lines
        $orderLineOrderIdField = $orderLineTableName . '.' . $metadataOL->getSingleAssociationJoinColumnName('order');
        $orderIdField = $tableName . '.' . $metadata->getColumnName('id');
        $sqlQuery .= $qb->leftJoin($orderLineTableName, $orderLineOrderIdField, $orderIdField);
        
        // join order payments
        $orderPaymentOrderIdField = $orderPaymentTableName . '.' . $metadataPayment->getSingleAssociationJoinColumnName('order');
        $orderIdField = $tableName . '.' . $metadata->getColumnName('id');
        $sqlQuery .= $qb->leftJoin($orderPaymentTableName, $orderPaymentOrderIdField, $orderIdField);
        
        // join order status
//        $orderStatusOrderIdField = $orderStatusTableName . '.' . $metadataStatus->getSingleAssociationJoinColumnName('order');
//        $orderIdField = $tableName . '.' . $metadata->getColumnName('id');
//        $sqlQuery .= $qb->leftJoin($orderStatusTableName, $orderStatusOrderIdField, $orderIdField);
              
        $whereRestaurant = sprintf('%s = %s', $metadata->getColumnName('restaurant_id'), $restaurant->getId());
        $sqlQuery .= $qb->where($whereRestaurant);
        
        if (isset($filters['start_date']) && $filters['start_date'] instanceof \DateTime) {
            $whereStartDate = sprintf('DATE(%s) >= "%s"', $metadata->getColumnName('orderDate'), $filters['start_date']->format('Y-m-d'));
            $sqlQuery .= $qb->andWhere($whereStartDate);
        }
        if (isset($filters['end_date']) && $filters['end_date'] instanceof \DateTime) {
            $whereStartDate = sprintf('DATE(%s) <= "%s"', $metadata->getColumnName('orderDate'), $filters['end_date']->format('Y-m-d'));
            $sqlQuery .= $qb->andWhere($whereStartDate);
        }

        $sqlQuery .= $qb->groupBy(array('history_date'));
        $sqlQuery .= $qb->orderBy(array('history_date' => 'DESC'));
        
        $sqlQuery .= $qb->limit($firstResult, $maxResults);
                
        $result = array();
        try {
            $stmt = $this->em->getConnection()->query($sqlQuery);
            while ($row = $stmt->fetch()) {
                $row['date'] = \DateTime::createFromFormat("Y-m-d", $row['history_date']);
                $result[] = $row;
            }
        } catch (\Exception $ex) {
        }
        
        return $result;
    }
}
