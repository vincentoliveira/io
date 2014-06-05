<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\OrderBundle\Entity\Order;
use IO\OrderBundle\Enum\OrderStatusEnum;

/**
 * @Route("/order/stats")
 */
class StatsController extends Controller
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
     * @Inject("io.distribution_service")
     * @var \IO\OrderBundle\Service\DistributionService
     */
    public $distribSv;
    
    /**
     * @Route("/distribution", name="stats_distribution")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function distributionAction()
    {
        $restaurant = $this->userSv->getUserRestaurant();
        
        $distributions = array();
        $distributions['global_pie'] = $this->distribSv->getGlobalRepartition($restaurant, array(), "pie");
        $distributions['global_bar'] = $this->distribSv->getGlobalRepartition($restaurant, array(), "bar");
        
        return array(
            'distributions' => $distributions,
        );
    }
    
}
