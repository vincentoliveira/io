<?php

namespace IO\OrderBundle\Service\ChartGenerator;

/**
 * Abstract Chart Generator
 */
abstract class AbstractChartGenerator implements ChartGeneratorInterface {
    
    protected $title = "";
    protected $xAxisTitle = "";
    protected $yAxisTitle = "";
    protected $series = array();
    
    /**
     * @{inheritDoc}
     */
    public function setTitle($title) {
        $this->title = $title;
    }
    
    /**
     * @{inheritDoc}
     */
    public function setXAxisTitle($title) {
        $this->xAxisTitle = $title;
    }
    
    /**
     * @{inheritDoc}
     */
    public function setYAxisTitle($title) {
        $this->yAxisTitle = $title;
    }
    
    /**
     * @{inheritDoc}
     */
    public function addSerie($serieName, array $serieData) {
        $this->series[] = array(
            'name' => $serieName,
            'data' => $serieData,
        );
    }
    
}
