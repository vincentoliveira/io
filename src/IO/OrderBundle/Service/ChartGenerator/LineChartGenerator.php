<?php

namespace IO\OrderBundle\Service\ChartGenerator;

use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * Line Chart Generator
 */
class LineChartGenerator extends AbstractChartGenerator {
    
    protected $categories = array();

    /**
     * @{inheritDoc}
     */
    public function addSerie($serieName, array $serieData) {
        
        $this->categories = array();
        $data = array();
        foreach ($serieData as $serie) {
            $this->categories[] = $serie[0];
            $data[] = $serie[1];
        }
        
        $this->series[] = array(
            'name' => $serieName,
            'data' => $data,
        );
    }
    
    /**
     * @{inheritDoc}
     */
    public function generate($id) {
        $ob = new Highchart();
        $ob->chart->renderTo($id);
        $ob->chart->type('line');
        $ob->title->text($this->title);
        $ob->xAxis->title(array('text'  => $this->yAxisTitle));
        $ob->xAxis->categories($this->categories);
        $ob->yAxis->title(array('text'  => $this->xAxisTitle));
        $ob->legend->enabled(false);
        $ob->series($this->series);
        
        return $ob;
    }
}
