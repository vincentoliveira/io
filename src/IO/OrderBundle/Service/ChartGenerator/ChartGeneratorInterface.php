<?php

namespace IO\OrderBundle\Service\ChartGenerator;

/**
 * Chart Generator Interface
 */
interface ChartGeneratorInterface {
    
    /**
     * Set chart title
     * 
     * @param stirng $title Chart title
     */
    public function setTitle($title);
    
    /**
     * Set xAxis chart title
     * 
     * @param stirng $title Chart xAxis title
     */
    public function setXAxisTitle($title);
    
    /**
     * Set yAxis chart title
     * 
     * @param stirng $title Chart yAxis title
     */
    public function setYAxisTitle($title);
    
    /**
     * Add a serie to the chart
     * 
     * @param string $serieName Chart data
     * @param array $serieData Chart data
     */
    public function addSerie($serieName, array $serieData);
    
    /**
     * Generate chart to id
     * 
     * @param string $id
     */
    public function generate($id);
    
}
