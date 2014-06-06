<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\CarteItem;
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
    public function getGlobalRepartition(Restaurant $restaurant, $graph = "pie", $id = "global_repartition") {
        $filters['restaurant_id'] = $restaurant->getId();
        
        $calculator = new DistributionCalculator();
        $serie = $calculator->calculate($this->em, $filters);
        
        if ($graph === "bar") {
            $chartGenerator = new BarChartGenerator();
        } else {
            $chartGenerator = new PieChartGenerator();
        }
        $chartGenerator->setTitle("Répartition globale");
        $chartGenerator->addSerie('Répartition', $serie);
        
        return $chartGenerator->generate($id);
    }

    /**
     * process order from data
     * 
     * @param array $data
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\OrderBundle\Entity\Order
     */
    public function getCategoryRepartition(Restaurant $restaurant, CarteItem $category, $graph = "pie", $id = "category_repartition") {
        $filters['restaurant_id'] = $restaurant->getId();
        $filters['parent_id'] = $category->getId();
        
        $calculator = new DistributionCalculator();
        $serie = $calculator->calculate($this->em, $filters);
        
        if ($graph === "bar") {
            $chartGenerator = new BarChartGenerator();
        } else {
            $chartGenerator = new PieChartGenerator();
        }
        $chartGenerator->setTitle("Répartition - " . $category->getName());
        $chartGenerator->addSerie('Répartition', $serie);
        
        return $chartGenerator->generate($id);
    }


}
