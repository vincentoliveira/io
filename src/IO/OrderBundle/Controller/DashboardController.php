<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * @Route("/dashboard")
 */
class DashboardController extends Controller
{
    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;
    
    /**
     * CarteItem Service
     * 
     * @Inject("io.history_service")
     * @var \IO\OrderBundle\Service\HistoryService
     */
    public $historySv;
    
    /**
     * @Route("/", name="dashboard")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function indexAction()
    {
        $restaurant = $this->userSv->getCurrentRestaurant();
        $history = $this->historySv->getOrderHistoryPerDay($restaurant, array('end_date' => new \DateTime()));
        if (empty($history)) {
            $dayHistory = array(
                'count' => 0,
                'total' => 0,
                'count_payed' => 0,
                'total_payed' => 0,
            );
        } else {
            $dayHistory = $history[0];
        }
        
        $tiles = array();
        $tiles[] = array(
            'title' => 'Commandes',
            'value' => sprintf('%d (%.2f€)', $dayHistory['count'], $dayHistory['total']),
        );
        $tiles[] = array(
            'title' => 'En attente',
            'value' => sprintf('%d (%.2f€)', $dayHistory['count'] - $dayHistory['count_payed'], $dayHistory['total'] - $dayHistory['total_payed']),
        );
        $tiles[] = array(
            'title' => 'Encaissées',
            'value' => sprintf('%d (%.2f€)', $dayHistory['count_payed'], $dayHistory['total_payed']),
        );
        $tiles[] = array(
            'title' => 'Panier moyen',
            'value' => sprintf('%.2f€', $dayHistory['count'] != 0 ? $dayHistory['total'] / $dayHistory['count'] : 0),
        );
        
        return array(
            'tiles' => $tiles,
        );
    }
}
