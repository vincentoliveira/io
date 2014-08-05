<?php

namespace IO\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\ApiBundle\Utils\ApiElementVisitor;


/**
 * Order API Controller
 * 
 * @Route("/order")
 */
class OrderController extends DefaultController
{

    /**
     * CarteItem Service
     * 
     * @Inject("io.order_service")
     * @var \IO\OrderBundle\Service\OrderService
     */
    public $orderSv;
    
    
    /**
     * GET /order/cart/:id.json
     * 
     * Add product to a desired cart and return new cart
     * 
     * Parameters:
     * - <strong>token</strong>         The alphanumeric token of the 
     *                                  user/platform.
     * - <strong>cart_id</strong>       The numerical id of the desired 
     *                                  cart
     * 
     * @return JsonResponse
     * @Route("/cart/{cartId}.json", name="api_order_get_cart")
     * @Method("GET")
     */
    public function getCartAction(Request $request, $cartId)
    {
        $em = $this->getDoctrine()->getManager();
        
        // check token
        $token = $request->query->get('token', null);
        if ($token === null) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $authTokenRepo = $em->getRepository("IOApiBundle:AuthToken");
        $authToken = $authTokenRepo->findOneByToken($token);
        if ($authToken === null || $authToken->hasExpired()) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        // get cart
        $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
        $cart = $orderRepo->find($cartId);
        if ($cart === null || !$authToken->getRestrictedRestaurants()->contains($cart->getRestaurant())) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('cart' => $cart->accept($apiVisistor)));
    }
    
    
    /**
     * POST /order/cart/create.json
     * 
     * Create a cart and return it.
     * 
     * Parameters:
     * - <strong>token</strong>         The alphanumeric token of the 
     *                                  user/platform.
     * - <strong>restaurant_id</strong>  The numerical id of the desired 
     *                                  restaurant (optionnal)
     * - <strong>product_id</strong>    The numerical id of the desired product
     *                                  (optionnal). If not set, cart will be 
     *                                  created as empty.
     * - <strong>options</strong>       An array of the ids of the desired 
     *                                  options if necessary (optionnal)
     * 
     * @return JsonResponse
     * @Route("/cart/create.json", name="api_order_create_cart")
     * @Method("POST")
     */
    public function createCartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        // check token
        $token = $request->request->get('token', null);
        if ($token === null) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $authTokenRepo = $em->getRepository("IOApiBundle:AuthToken");
        $authToken = $authTokenRepo->findOneByToken($token);
        if ($authToken === null || $authToken->hasExpired()) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        // get restaurant
        $restaurantId = $request->request->get('restaurant_id', null);
        if ($restaurantId !== null) {
            $restaurantRepo = $em->getRepository("IORestaurantBundle:Restaurant");
            $restaurant = $restaurantRepo->find($restaurantId);
        } else {
            $restaurant = $authToken->getRestaurant();
        }
        
        if ($restaurant === null || !$authToken->getRestrictedRestaurants()->contains($restaurant)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $cart = $this->orderSv->createOrder($restaurant, $authToken);
        if ($request->request->has('product_id')) {
            $productId = $request->request->get('product_id');
            $options = $request->request->get('options');
            $cart = $this->orderSv->addProductToOrder($cart, $productId, $options);
        }
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('cart' => $cart->accept($apiVisistor)));
    }
    
    
    /**
     * POST /order/cart/add_product.json
     * 
     * Add product to a desired cart and return new cart
     * 
     * Parameters:
     * - <strong>token</strong>         The alphanumeric token of the 
     *                                  user/platform.
     * - <strong>cart_id</strong>       The numerical id of the desired 
     *                                  cart
     * - <strong>product_id</strong>    The numerical id of the desired product
     *                                  (optionnal). If not set, cart will be 
     *                                  created as empty.
     * - <strong>options</strong>       An array of the ids of the desired 
     *                                  options if necessary (optionnal)
     * 
     * @return JsonResponse
     * @Route("/cart/add_product.json", name="api_order_add_product_to_cart")
     * @Method("POST")
     */
    public function addProductToCartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        // check token
        $token = $request->request->get('token', null);
        if ($token === null) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $authTokenRepo = $em->getRepository("IOApiBundle:AuthToken");
        $authToken = $authTokenRepo->findOneByToken($token);
        if ($authToken === null || $authToken->hasExpired()) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        // get cart
        $cartId = $request->request->get('cart_id', null);
        if ($cartId !== null) {
            $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
            $cart = $orderRepo->find($cartId);
        }
        
        if ($cart === null || !$authToken->getRestrictedRestaurants()->contains($cart->getRestaurant())) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        if ($this->orderSv->isLocked($cart)) {
            return $this->errorResponse(self::ORDER_LOCKED);
        }
        
        if (!$request->request->has('product_id')) {
            return $this->errorResponse(self::BAD_PARAMETER);
        }
        
        $productId = $request->request->get('product_id');
        $options = $request->request->get('options');
        $cart = $this->orderSv->addProductToOrder($cart, $productId, $options);
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('cart' => $cart->accept($apiVisistor)));
    }
}
