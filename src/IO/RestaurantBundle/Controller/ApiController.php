<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * Description of ApiController
 * 
 * @Route("/api")
 */
class ApiController extends Controller
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
     * @Inject("io.carte_item_service")
     * @var \IO\RestaurantBundle\Service\CarteItemService
     */
    public $carteItemSv;
    
    /**
     * Admin restaurant index
     * 
     * @return type
     * @Route("/carte.json", name="restaurant_api_get_carte")
     * @Secure(roles="ROLE_TABLETTE")
     */
    public function getCarteAction()
    {
        $restaurant = $this->userSv->getUserRestaurant(); 
        $carte = $this->carteItemSv->getCarte($restaurant);
        return new JsonResponse(array('carte' => $carte));
    }
}
