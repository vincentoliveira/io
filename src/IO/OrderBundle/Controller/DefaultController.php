<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller
{    
    /**
     * Order en cours
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @Secure(roles="ROLE_CUISINIER")
     */
    public function commandeEnCoursAction()
    {
        $userSv = $this->get('user.user_service');
        $restaurant = $userSv->getUserRestaurant();
        
        $repo = $this->getDoctrine()->getRepository('IOOrderBundle:Order');
        $orders = $repo->getOrdersInProgress($restaurant);
        
        return array(
            'orders' => $orders,
        );
    }
}
