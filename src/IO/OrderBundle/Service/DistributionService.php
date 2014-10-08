<?php

namespace IO\OrderBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Service\StatsCalculator\DistributionCalculator;
use IO\OrderBundle\Service\StatsCalculator\TimeDistributionCalculator;
use IO\OrderBundle\Service\StatsCalculator\PaymentDistributionCalculator;
use IO\OrderBundle\Service\StatsCalculator\TurnoverEvolutionCalculator;
use IO\OrderBundle\Service\ChartGenerator\LineChartGenerator;
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
     * Get Time Distribution
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param type $id
     * @return type
     */
    public function getTurnoverEvolution(array $filters = array(), $id = "turnover_evolution") {
        $calculator = new TurnoverEvolutionCalculator();
        $serie = $calculator->calculate($this->em, $filters);
        
        $chartGenerator = new LineChartGenerator();
        $chartGenerator->setTitle("Evolution du chiffre d'affaire");
        $chartGenerator->addSerie('Chiffre d\'affaire (en €)', $serie);
        
        return $chartGenerator->generate($id);
    }

    /**
     * Get Global Repartition
     * 
     * @param array $filters
     * @param type $graph
     * @param type $id
     * @return type
     */
    public function getGlobalDistribution(array $filters, $graph = "pie", $id = "global_repartition") {        
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
     * @param array $filters
     * @param type $graph
     * @param type $id
     * @return type
     */
    public function getCategoryDistribution(array $filters, $graph = "pie", $id = "category_repartition", $categoryName = "") {
        $calculator = new DistributionCalculator();
        $serie = $calculator->calculate($this->em, $filters);
        
        if ($graph === "bar") {
            $chartGenerator = new BarChartGenerator();
        } else {
            $chartGenerator = new PieChartGenerator();
        }
        $chartGenerator->setTitle("Répartition - " . $categoryName);
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
