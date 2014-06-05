<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Service\StatsCalculator\DistributionCalculator;
use IO\OrderBundle\Service\ChartGenerator\PieChartGenerator;
use IO\OrderBundle\Service\ChartGenerator\BarChartGenerator;

/**
 * Distribution Service
 * 
 * @Service("io.distribution_service")
 */
class DistributionService {

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
    public function getGlobalRepartition(Restaurant $restaurant, $filters = array(), $graph = "pie") {
        $filters['restaurant'] = $restaurant->getId();
        
        $calculator = new DistributionCalculator();
        $serie = $calculator->calculate($this->em, $filters);
        
        if ($graph === "bar") {
            $chartGenerator = new BarChartGenerator();
        } else {
            $chartGenerator = new PieChartGenerator();
        }
        $chartGenerator->setTitle("Répartition globale");
        $chartGenerator->addSerie('Répartition', $serie);
        
        return $chartGenerator->generate('global_'. $graph);
    }


}
