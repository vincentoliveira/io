<?php

namespace IO\OrderBundle\Service;

use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * Chart Service
 */
class ChartService
{

    /**
     * Chart Title
     * 
     * @var string
     */
    protected $title = FALSE;

    /**
     * Chart Title
     * 
     * @var string
     */
    protected $xAxisTitle = FALSE;

    /**
     * Chart Title
     * 
     * @var string
     */
    protected $yAxisTitle = FALSE;
    
    /**
     * Set title
     * 
     * @param string $title
     * @return \IO\OrderBundle\Service\ChartService
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    /**
     * Set xAxis title
     * 
     * @param string $title
     * @return \IO\OrderBundle\Service\ChartService
     */
    public function setXAxisTitle($title)
    {
        $this->xAxisTitle = $title;
        return $this;
    }
    
    /**
     * Set yAxis title
     * 
     * @param string $title
     * @return \IO\OrderBundle\Service\ChartService
     */
    public function setYAxisTitle($title)
    {
        $this->yAxisTitle = $title;
        return $this;
    }
    
    /**
     * Generate "Courbe de regression" chart
     *
     * @param array $regLin
     * @param string $id
     * @return array
     */
    public function generateLine(array $data, array $abscisses = null, $id = 'chart')
    {
        if (empty($data)) {
            return null;
        }
        
        if ($abscisses === null) {
            $key = key($data);
            foreach ($data[$key] as $abscisse => $value) {
                $abscisses[] = $abscisse;
            }
        }
        
        $chart = new Highchart();
        $chart->credits->enabled(false);
        $chart->chart->renderTo($id);
        $chart->title->text($this->title);
        $chart->yAxis->title(array('text' => $this->yAxisTitle));
        $chart->xAxis->title(array('text' => $this->xAxisTitle));
        
        $series = array();
        foreach ($data as $name => $serieData) {
            $serie = array();
            foreach ($serieData as $d) {
                $serie[] = $d;
            } 
            $series[] = array("name" => $name, "data" => $serie);
        }
        $chart->series($series);
        $chart->xAxis->categories($abscisses);
        
        return $chart;
    }
}
