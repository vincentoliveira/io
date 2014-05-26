<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * @Route("/order")
 */
class DefaultController extends Controller
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
     * @Inject("io.order_service")
     * @var \IO\OrderBundle\Service\OrderService
     */
    public $orderSv;
    
    /**
     * @Route("/", name="order_index")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function indexAction()
    {
        $restaurant = $this->userSv->getUserRestaurant();
        $orders = $this->orderSv->getCurrentOrders($restaurant);
        return array(
            'orders' => $orders,
        );
    }
}
