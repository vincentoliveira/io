<?php

namespace IO\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\ApiBundle\Utils\ApiElementVisitor;

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
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;
    
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
     * - <strong>token</strong> The alphanumeric token of the user who wants to
     *                          access his restaurant.
     * 
     * 
     * @return JsonResponse
     * @Route("/menu/token/{token}.json", name="api_restaurant_get_menu_by_token")
     */
    public function getCarteByTokenAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("IOApiBundle:AuthToken");
        $userToken = $repo->findOneByToken($token);
        if ($userToken === null || $userToken->hasExpired() || $userToken->getRestaurant() === null) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $carte = $this->carteItemSv->getCarte($userToken->getRestaurant());
        return new JsonResponse(array('carte' => $carte));
    }
    
    /**
     * POST /restaurant/auth.json
     * 
     * Authentificate a restaurant from its manager login/paasword.
     * Return auth token
     * 
     * Parameters:
     * - <strong>email</strong> Email of the user you want to authenticate 
     *                          (string)
     * - <stroong>plainPassword</strong> Plain password of the user you want to 
     *                                   authenticate (string)
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/auth.json", name="api_restaurant_auth")
     * @Method("POST")
     */
    public function authAction(Request $request)
    {
        $data = $request->request->all();
        if ($data === null || empty($data)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }

        $userToken = $this->userSv->authUser($data);
        if ($userToken === null || $userToken->getUser()->getRestaurant() === null) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }

        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('auth' => $apiVisistor->visitAuthToken($userToken)));
    }
}
