<?php

namespace IO\OrderBundle\Service\QueryBuilder;

/**
 * MySQL Query Builder
 */
class MySQLQueryBuilder implements QueryBuilderInterface {

    /**
     * @{inheritDoc}
     */
    public function select($fields) {
        $result = '';
        foreach ($fields as $field => $alias) {
            $result .= ', ' . $field . ' as "' . $alias . '"';
        }

        return 'SELECT' . substr($result, 1) . ' ';
    }

    /**
     * @{inheritDoc}
     */
    public function from($tableName) {
        return sprintf('FROM %s ', $tableName);
    }

    /**
     * @{inheritDoc}
     */
    public function where($whereClauses) {
        return sprintf('WHERE %s ', $whereClauses);
    }

    /**
     * @{inheritDoc}
     */
    public function leftJoin($joinTable, $joinField, $parentField) {
        return sprintf('LEFT JOIN %s ON %s = %s ', $joinTable, $joinField, $parentField);
    }

    /**
     * @{inheritDoc}
     */
    public function limit($firstResult, $maxResults) {
        return sprintf('LIMIT %s, %s ', $firstResult, $maxResults);
    }

    /**
     * @{inheritDoc}
     */
    public function groupBy($fields) {
        $result = '';
        foreach ($fields as $field) {
            $result .= ', ' . $field;
        }

        return 'GROUP BY' . substr($result, 1) . ' ';
    }

    /**
     * @{inheritDoc}
     */
    public function orderBy($fields) {
        $result = '';
        foreach ($fields as $field => $direction) {
            $result .= ', ' . $field . ' ' . $direction;
        }

        return 'ORDER BY' . substr($result, 1) . ' ';
    }

}
