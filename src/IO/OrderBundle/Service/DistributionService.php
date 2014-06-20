<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Service\StatsCalculator\DistributionCalculator;
use IO\OrderBundle\Service\StatsCalculator\TimeDistributionCalculator;
use IO\OrderBundle\Service\StatsCalculator\PaymentDistributionCalculator;
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
     * Get Global Repartition
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param type $graph
     * @param type $id
     * @return type
     */
    public function getGlobalDistribution(Restaurant $restaurant, $graph = "pie", $id = "global_repartition") {
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
     * Get Category Distribution
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param \IO\RestaurantBundle\Entity\CarteItem $category
     * @param type $graph
     * @param type $id
     * @return type
     */
    public function getCategoryDistribution(Restaurant $restaurant, CarteItem $category, $graph = "pie", $id = "category_repartition") {
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

    /**
     * Get Time Distribution
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param type $id
     * @return type
     */
    public function getTimeDistribution(Restaurant $restaurant, $id = "time_repartition") {
        $filters['restaurant_id'] = $restaurant->getId();
        
        $calculator = new TimeDistributionCalculator();
        $serie = $calculator->calculate($this->em, $filters);
        
        $chartGenerator = new BarChartGenerator();
        $chartGenerator->setTitle("Répartition en fonction du temps");
        $chartGenerator->addSerie('Répartition', $serie);
        
        return $chartGenerator->generate($id);
    }
    

    /**
     * Get Time Distribution
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param type $id
     * @return type
     */
    public function getPaymentDistribution(Restaurant $restaurant, $id = "payment_repartition") {
        $filters['restaurant_id'] = $restaurant->getId();
        
        $calculator = new PaymentDistributionCalculator();
        $serie = $calculator->calculate($this->em, $filters);
        
        $chartGenerator = new PieChartGenerator();
        $chartGenerator->setTitle("Répartition des modes de paiement (Nombre)");
        $chartGenerator->addSerie('Répartition', $serie);
        
        return $chartGenerator->generate($id);
    }

    /**
     * Get Time Distribution
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param type $id
     * @return type
     */
    public function getPaymentAmountDistribution(Restaurant $restaurant, $id = "payment_amount_repartition") {
        $filters['restaurant_id'] = $restaurant->getId();
        
        $calculator = new PaymentDistributionCalculator();
        $serie = $calculator->calculateAmount($this->em, $filters);
        
        $chartGenerator = new PieChartGenerator();
        $chartGenerator->setTitle("Répartition des modes de paiement (Montants)");
        $chartGenerator->addSerie('Répartition', $serie);
        
        return $chartGenerator->generate($id);
    }


}
