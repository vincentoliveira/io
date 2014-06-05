<?php

namespace IO\OrderBundle\Service\StatsCalculator;

/**
 * Stats Calculator Interface
 */
interface StatsCalculatorInterface {
    
    /**
     * Calculate stats
     * 
     * @param \Doctrine\ORM\EntityManager $em Entity Manager
     * @param array $filters Filters
     */
    public function calculate(\Doctrine\ORM\EntityManager $em, $filters = array());
    
}
