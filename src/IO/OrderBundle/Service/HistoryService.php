<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Entity\Order;
use IO\OrderBundle\Entity\OrderLine;
use IO\OrderBundle\Enum\OrderStatusEnum;

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
     * @return \IO\OrderBundle\Entity\Order
     */
    public function getDayHistory(\DateTime $date, Restaurant $restaurant) {
        $repo = $this->em->getRepository('IOOrderBundle:Order');
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
     * process order from data
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\Order
     */
    public function getOrderHistoryPerDay(Restaurant $restaurant, $maxResults = 20, $firstResult = 0) {
        $metadata = $this->em->getClassMetadata('IOOrderBundle:Order');
        $metadataOL = $this->em->getClassMetadata('IOOrderBundle:OrderLine');

        $tableName = $metadata->getTableName();
        $orderLineTableName = $metadataOL->getTableName();
        
        $sqlQuery = $this->select(array(
            sprintf('COUNT(DISTINCT %s.%s)', $tableName, $metadata->getColumnName('id')) => 'count',
            sprintf('DATE(%s.%s)', $tableName, $metadata->getColumnName('orderDate')) => 'date',
            sprintf('SUM(%s.%s)', $orderLineTableName, $metadataOL->getColumnName('itemPrice')) => 'total',
            sprintf('AVG(TIMESTAMPDIFF(SECOND,%s.%s,%1$s.%s))', $tableName, $metadata->getColumnName('startDate'), $metadata->getColumnName('orderDate')) => 'avgOrderTime',
        ));
        $sqlQuery .= $this->from($tableName);
        
        $orderLineOrderIdField = $orderLineTableName . '.' . $metadataOL->getSingleAssociationJoinColumnName('order');
        $orderIdField = $tableName . '.' . $metadata->getColumnName('id');
        $sqlQuery .= $this->leftJoin($orderLineTableName, $orderLineOrderIdField, $orderIdField);
              
        $whereRestaurant = sprintf('%s = %s', $metadata->getColumnName('restaurant_id'), $restaurant->getId());
        $sqlQuery .= $this->where($whereRestaurant);

        $sqlQuery .= $this->groupBy(array('date'));
        $sqlQuery .= $this->orderBy(array('date' => 'DESC'));
        
        $sqlQuery .= $this->limit($firstResult, $maxResults);
        
        $result = array();
        try {
            $stmt = $this->em->getConnection()->query($sqlQuery);
            while ($row = $stmt->fetch()) {
                $row['date'] = \DateTime::createFromFormat("Y-m-d", $row['date']);
                $result[] = $row;
            }
        } catch (\Exception $ex) {
        }

        return $result;
    }

    protected function select($fields) {
        $result = '';
        foreach ($fields as $field => $alias) {
            $result .= ', ' . $field . ' as "' . $alias . '"';
        }

        return 'SELECT' . substr($result, 1) . ' ';
    }

    protected function from($tableName) {
        return sprintf('FROM %s ', $tableName);
    }

    protected function where($whereClauses) {
        return sprintf('WHERE %s ', $whereClauses);
    }

    protected function leftJoin($joinTable, $joinField, $parentField) {
        return sprintf('LEFT JOIN %s ON %s = %s ', $joinTable, $joinField, $parentField);
    }

    protected function limit($firstResult, $maxResults) {
        return sprintf('LIMIT %s, %s ', $firstResult, $maxResults);
    }

    protected function groupBy($fields) {
        $result = '';
        foreach ($fields as $field) {
            $result .= ', ' . $field;
        }

        return 'GROUP BY' . substr($result, 1) . ' ';
    }

    protected function orderBy($fields) {
        $result = '';
        foreach ($fields as $field => $direction) {
            $result .= ', ' . $field . ' ' . $direction;
        }

        return 'ORDER BY' . substr($result, 1) . ' ';
    }

}
