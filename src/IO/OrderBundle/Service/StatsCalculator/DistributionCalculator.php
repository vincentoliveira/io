<?php

namespace IO\OrderBundle\Service\StatsCalculator;

use Doctrine\ORM\EntityManager;
use IO\OrderBundle\Service\QueryBuilder\MySQLQueryBuilder;

/**
 * Distribution Calculator
 */
class DistributionCalculator implements StatsCalculatorInterface {
 
    /**
     * @{inheritDoc}
     */
    public function calculate(EntityManager $em, $filters = array()) {
        if (!isset($filters['restaurant_id'])) {
            return array();
        }
        
        $itemMetadata = $em->getClassMetadata('IORestaurantBundle:CarteItem');
        $lineMetadata = $em->getClassMetadata('IOOrderBundle:OrderLine');
        
        $itemTableName = $itemMetadata->getTableName();
        $lineTableName = $lineMetadata->getTableName();
        
        $qb = new MySQLQueryBuilder();
                
        $sqlQuery = $qb->select(array(
            sprintf('COUNT(DISTINCT %s.%s)', $lineTableName, $lineMetadata->getColumnName('id')) => 'count',
            sprintf('%s.%s', $itemTableName, $itemMetadata->getColumnName('name')) => 'item_name',
        ));
        $sqlQuery .= $qb->from($itemTableName);
        
        $itemFK = sprintf('%s.%s', $lineTableName, $lineMetadata->getSingleAssociationJoinColumnName('item'));
        $parentId = sprintf('%s.%s', $itemTableName, $itemMetadata->getColumnName('id'));
        $sqlQuery .= $qb->leftJoin($lineTableName, $parentId, $itemFK);
                    
        $restaurantFK = sprintf('%s.%s', $itemTableName, $itemMetadata->getSingleAssociationJoinColumnName('restaurant'));
        $sqlQuery .= $qb->where(sprintf('%s = %s', $restaurantFK, $filters['restaurant_id']));
        
        if (isset($filters['parent_id'])) {
            $parentFK = sprintf('%s.%s', $itemTableName, $itemMetadata->getSingleAssociationJoinColumnName('parent'));
            $sqlQuery .= $qb->andWhere(sprintf('%s = %s', $parentFK, $filters['parent_id']));
        }
        
        $sqlQuery .= $qb->groupBy(array('item_name'));
        $sqlQuery .= $qb->orderBy(array('count' => 'DESC'));
        
        $result = array();
        try {
            $stmt = $em->getConnection()->query($sqlQuery);
            while ($row = $stmt->fetch()) {
                $result[] = array($row['item_name'], intval($row['count']));
            }
        } catch (\Exception $ex) {
        }

        return $result;
    }

}
