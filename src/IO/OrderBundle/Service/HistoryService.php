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
     * process order from data
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\OrderData
     */
    public function getOrderHistoryPerDay(Restaurant $restaurant, $maxResults = 20, $firstResult = 0) {
        $metadata = $this->em->getClassMetadata('IOOrderBundle:OrderData');
        $metadataOL = $this->em->getClassMetadata('IOOrderBundle:OrderLine');

        $tableName = $metadata->getTableName();
        $orderLineTableName = $metadataOL->getTableName();
        
        $qb = new MySQLQueryBuilder();
        
        $sqlQuery = $qb->select(array(
            sprintf('COUNT(DISTINCT %s.%s)', $tableName, $metadata->getColumnName('id')) => 'count',
            sprintf('DATE(%s.%s)', $tableName, $metadata->getColumnName('orderDate')) => 'date',
            sprintf('SUM(%s.%s)', $orderLineTableName, $metadataOL->getColumnName('itemPrice')) => 'total',
            sprintf('AVG(TIMESTAMPDIFF(SECOND,%s.%s,%1$s.%s))', $tableName, $metadata->getColumnName('startDate'), $metadata->getColumnName('orderDate')) => 'avgOrderTime',
        ));
        $sqlQuery .= $qb->from($tableName);
        
        $orderLineOrderIdField = $orderLineTableName . '.' . $metadataOL->getSingleAssociationJoinColumnName('order');
        $orderIdField = $tableName . '.' . $metadata->getColumnName('id');
        $sqlQuery .= $qb->leftJoin($orderLineTableName, $orderLineOrderIdField, $orderIdField);
              
        $whereRestaurant = sprintf('%s = %s', $metadata->getColumnName('restaurant_id'), $restaurant->getId());
        $sqlQuery .= $qb->where($whereRestaurant);

        $sqlQuery .= $qb->groupBy(array('date'));
        $sqlQuery .= $qb->orderBy(array('date' => 'DESC'));
        
        $sqlQuery .= $qb->limit($firstResult, $maxResults);
        
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
}
