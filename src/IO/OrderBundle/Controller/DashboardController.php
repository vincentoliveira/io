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
        
        $history = $history[0];
        $avgOrderTime = intval($history['avgOrderTime'] / 60) . 'min' . intval($history['avgOrderTime'] % 60);
        
        return array(
            'day' => $history['date'],
            'dayCount' => $history['count'],
            'dayTotal' => $history['total'],
            'avgOrderTime' => $avgOrderTime,
        );
    }
}
