<?php

namespace IO\OrderBundle\Service\QueryBuilder;

/**
 * Query Builder Interface
 */
interface QueryBuilderInterface {
    
    public function select($fields);

    public function from($tableName);

    public function where($whereClauses);
    
    public function andWhere($whereClauses);

    public function leftJoin($joinTable, $joinField, $parentField);

    public function limit($firstResult, $maxResults);
    
    public function groupBy($fields);

    public function orderBy($fields);

}
