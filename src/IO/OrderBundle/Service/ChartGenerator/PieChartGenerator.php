<?php

namespace IO\OrderBundle\Service\ChartGenerator;

use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * Pie Chart Generator
 */
class PieChartGenerator extends AbstractChartGenerator {
    
    
    /**
     * @{inheritDoc}
     */
    public function addSerie($serieName, array $serieData) {
        $this->series[] = array(
            'type' => 'pie',
            'name' => $serieName,
            'data' => $serieData,
        );
    }
    
    /**
     * @{inheritDoc}
     */
    public function generate($id) {
        $ob = new Highchart();
        $ob->chart->renderTo($id);
        $ob->title->text($this->title);
        $ob->series($this->series);
        
        return $ob;
    }
}
