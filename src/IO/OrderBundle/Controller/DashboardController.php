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
        $history = $this->historySv->getOrderHistoryPerDay($restaurant, 1);
        if (empty($history)) {
            return $this->redirect($this->generateUrl('order_index'));
        }
        
        $dayHistory = $history[0];
        $tiles = array();
        $tiles[] = array(
            'title' => 'Commandes',
            'value' => sprintf('%s (%s€)', $dayHistory['count'], $dayHistory['total']),
        );
        $tiles[] = array(
            'title' => 'En attente',
            'value' => sprintf('%s (%s€)', $dayHistory['count'] - $dayHistory['count_payed'], $dayHistory['total'] - $dayHistory['total_payed']),
        );
        $tiles[] = array(
            'title' => 'Encaissées',
            'value' => sprintf('%s (%s€)', $dayHistory['count_payed'], $dayHistory['total_payed']),
        );
        $tiles[] = array(
            'title' => 'Panier moyen',
            'value' => sprintf('%s€', $dayHistory['total'] / $dayHistory['count']),
        );
        
        return array(
            'day' => $dayHistory['date'],
            'tiles' => $tiles,
        );
    }
}
