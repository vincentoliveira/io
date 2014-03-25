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
                "value" => "192,50€",
                "class" => "tile-red",
                "icon" => "fa fa-bar-chart-o",
            ),
            array(
                "title" => "Nombre de couverts",
                "value" => "8",
                "class" => "tile-blue",
                "icon" => "fa fa-user",
            ),
            array(
                "title" => "Durée moyenne par table",
                "value" => "17'32\"",
                "class" => "tile-purple",
                "icon" => "fa fa-clock-o",
            ),
        );
        
        return array(
            'indicateurs' => $indicateurs,
        );
    }
}
