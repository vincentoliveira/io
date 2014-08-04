<?php

namespace IO\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * Restaurant API Controller
 * 
 * @Route("/restaurant")
 */
class RestaurantController extends DefaultController
{
    
    /**
     * CarteItem Service
     * 
     * @Inject("io.carte_item_service")
     * @var \IO\RestaurantBundle\Service\CarteItemService
     */
    public $carteItemSv;
    
    /**
     * GET /restaurant/menu/:id.json
     * 
     * Returns a restaurant menu, specified by the <strong>id</strong> 
     * parameter.
     * 
     * Parameters:
     * - <strong>id</strong> The numerical ID of the desired restaurant.
     * 
     * 
     * @return JsonResponse
     * @Route("/menu/{id}.json", name="api_restaurant_get_menu")
     */
    public function getCarteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("IORestaurantBundle:Restaurant");
        $restaurant = $repo->find($id);
        if ($restaurant === null) {
            return $this->errorResponse(self::BAD_PARAMETER);
        }
        
        $carte = $this->carteItemSv->getCarte($restaurant);
        return new JsonResponse(array('carte' => $carte));
    }
    
    /**
     * GET /restaurant/menu/token/:token.json
     * 
     * Returns a restaurant menu, specified by the <strong>id</strong> 
     * parameter.
     * 
     * Parameters:
     * - <strong>id</strong> The numerical ID of the desired restaurant.
     * 
     * 
     * @return JsonResponse
     * @Route("/menu/token/:token.json", name="api_restaurant_get_menu_by_token")
     */
    public function getCarteByTokenAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("IOApiBundletBundle:UserToken");
        $userToken = $repo->findByToken($token);
        if ($userToken === null || $userToken->hasExpired() || $userToken->getRestaurant() === null) {
            return $this->errorResponse(self::BAD_PARAMETER);
        }
        
        $carte = $this->carteItemSv->getCarte($userToken->getRestaurant());
        return new JsonResponse(array('carte' => $carte));
    }
    
}
