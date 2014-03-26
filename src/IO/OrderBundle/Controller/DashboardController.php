<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Dashboard Controller
 */
class DashboardController extends Controller
{    
    /**
     * Order en cours
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function indexAction()
    {
        $indicateurs = array(
            array(
                "title" => "Chiffre d'affaire du jour",
                "value" => "492,50€",
                "class" => "tile-red",
                "icon" => "fa fa-bar-chart-o",
            ),
            array(
                "title" => "Nombre de couverts",
                "value" => "37",
                "class" => "tile-blue",
                "icon" => "fa fa-user",
            ),
            array(
                "title" => "Durée moyenne par table",
                "value" => "17'32\"",
                "class" => "tile-purple",
                "icon" => "fa fa-clock-o",
            ),
            array(
                "title" => "Bénéfice net",
                "value" => "321,28€",
                "class" => "tile-turquoise",
                "icon" => "fa fa-money",
            ),
        );
        
        $chartSv = $this->container->get('order.chart_service');
        $chartSv->setTitle("Chiffre d'affaire des 30 dernières jours");
        $chartSv->setYAxisTitle("Chiffre d'affaire (€)");
        
        $dateStart = new \DateTime();
        $dateStart->sub(new \DateInterval('P31D'));
        $dateMed = new \DateTime();
        $dateMed->sub(new \DateInterval('P15D'));
        $dateEnd = new \DateTime();
        $dateEnd->sub(new \DateInterval('P1D'));
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($dateStart, $interval, $dateEnd);
        
        $data = array();
        $abscisses = array();
        foreach ($period as $dt) {
            $data[$dt->format('d/m/Y')] = 800.0 + (mt_rand(0, 1000) / 10);
            $abscisses[] = '';
        }
        
        $abscisses[0] = $dateStart->format('d/m/Y');
        $abscisses[14] = $dateMed->format('d/m/Y');
        $abscisses[29] = $dateEnd->format('d/m/Y');
        
        $caChart = $chartSv->generateLine(array("Chiffre d'affaire" => $data), $abscisses, 'ca_chart');
        
        return array(
            'indicateurs' => $indicateurs,
            'ca_chart' => $caChart,
        );
    }
}
