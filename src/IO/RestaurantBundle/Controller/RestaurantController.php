<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Form\RestaurantGroupType;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\RestaurantBundle\Entity\RestaurantGroup;

/**
 * Restaurant Controller
 * 
 * @Route("/restaurant")
 */
class RestaurantController extends Controller 
{

    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;
    
    /**
     * Admin restaurant index
     * 
     * @return type
     * @Route("/current/{restaurantId}", name="restaurant_set_current")
     * @Secure(roles="ROLE_CHIEF")
     * @Template()
     */
    public function setCurentRestaurantAction(Request $request, $restaurantId) {
        $repo = $this->getDoctrine()->getRepository('IORestaurantBundle:Restaurant');
        $restaurant = $repo->find($restaurantId);
        if ($this->userSv->getUser()->getRestaurantGroup() === $restaurant->getGroup()) {
            $this->userSv->setCurrentRestaurant($restaurant);
        }

        $session = $request->getSession();
        $session->set("user.restaurant", $restaurant->getId());
        $session->save();
        
        return $this->forward('IODefaultBundle:Default:index');
    }

}
