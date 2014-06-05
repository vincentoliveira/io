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
        $metadata = $em->getClassMetadata('IOOrderBundle:OrderLine');
        $tableName = $metadata->getTableName();
        
        $qb = new MySQLQueryBuilder();
                
        //$itemFK = $tableName . '.' . $metadata->getSingleAssociationJoinColumnName('item');
        $sqlQuery = $qb->select(array(
            sprintf('COUNT(DISTINCT %s.%s)', $tableName, $metadata->getColumnName('id')) => 'count',
            $metadata->getColumnName('itemShortName') => 'item_name',
        ));
        $sqlQuery .= $qb->from($tableName);
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
