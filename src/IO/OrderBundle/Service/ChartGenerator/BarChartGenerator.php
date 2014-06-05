<?php

namespace IO\OrderBundle\Service\ChartGenerator;

use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * Pie Chart Generator
 */
class BarChartGenerator extends AbstractChartGenerator {
    
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
        $ob->chart->type('column');
        $ob->title->text($this->title);
        $ob->xAxis->title(array('text'  => $this->yAxisTitle, 'categories' => $this->categories));
        $ob->yAxis->title(array('text'  => $this->xAxisTitle));
        $ob->series($this->series);
        
        return $ob;
    }
}
