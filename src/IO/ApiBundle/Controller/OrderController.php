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
        // get restaurant
        $restaurant = null;
        $restaurantId = $request->request->get('restaurant_id', null);
        if ($restaurantId !== null) {
            $em = $this->getDoctrine()->getManager();
            $restaurantRepo = $em->getRepository("IORestaurantBundle:Restaurant");
            $restaurant = $restaurantRepo->find($restaurantId);
        }
        
        // check token
        $token = $request->request->get('token', null);
        if (!$this->checkToken($token, $restaurant)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        if ($restaurant === null) {
            $restaurant = $this->authToken->getRestaurant();
        }
        
        $cart = $this->orderSv->createOrder($restaurant, $this->authToken);
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
     *                                  to be removed
     * - <strong>options</strong>       An array of the ids of the desired 
     *                                  options if necessary (optionnal)
     * 
     * @return JsonResponse
     * @Route("/cart/add_product.json", name="api_order_add_product_to_cart")
     * @Method("POST")
     */
    public function addProductToCartAction(Request $request)
    {
        // get cart
        $cartId = $request->request->get('cart_id', null);
        if ($cartId !== null) {
            $em = $this->getDoctrine()->getManager();
            $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
            $cart = $orderRepo->find($cartId);
        }
        
        // check token
        $token = $request->request->get('token', null);
        if ($cart === null || !$this->checkToken($token, $cart->getRestaurant())) {
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
    
    
    /**
     * DELETE /order/cart/remove_product.json
     * 
     * Remove product from a desired cart and return new cart
     * 
     * Parameters:
     * - <strong>token</strong>         The alphanumeric token of the 
     *                                  user/platform.
     * - <strong>cart_id</strong>       The numerical id of the desired 
     *                                  cart
     * - <strong>product_id</strong>    The numerical id of the desired product
     *                                  to be removed.
     * - <strong>extra</strong>         The desired product to be removed extra
     * 
     * @return JsonResponse
     * @Route("/cart/remove_product.json", name="api_order_remove_product_from_cart")
     * @Method("DELETE")
     */
    public function removeProductToCartAction(Request $request)
    {
        // get cart
        $cartId = $request->request->get('cart_id', null);
        if ($cartId !== null) {
            $em = $this->getDoctrine()->getManager();
            $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
            $cart = $orderRepo->find($cartId);
        }
        
        // check token
        $token = $request->request->get('token', null);
        if ($cart === null || !$this->checkToken($token, $cart->getRestaurant())) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        if ($this->orderSv->isLocked($cart)) {
            return $this->errorResponse(self::ORDER_LOCKED);
        }
        
        if (!$request->request->has('product_id')) {
            return $this->errorResponse(self::BAD_PARAMETER);
        }
        
        $productId = $request->request->get('product_id');
        $extra = $request->request->get('extra');
        $cart = $this->orderSv->removeProductFromOrder($cart, $productId, $extra);
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('cart' => $cart->accept($apiVisistor)));
    }
}
