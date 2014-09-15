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
 * @Route("/order/cart")
 */
class CartController extends DefaultController
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
     * @Route("/{cartId}.json", name="api_order_get_cart")
     * @Method("GET")
     */
    public function getCartAction(Request $request, $cartId)
    {
        $em = $this->getDoctrine()->getManager();
        
        // check token
        $token = $request->query->get('token', null);
        $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
        $cart = $orderRepo->find($cartId);
        if ($cart === null || !$this->checkRestaurantToken($token, $cart->getRestaurant())) {
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
     * @Route("/create.json", name="api_order_create_cart")
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
        if (!$this->checkRestaurantToken($token, $restaurant)) {
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
     * @Route("/add_product.json", name="api_order_add_product_to_cart")
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
        if ($cart === null || !$this->checkRestaurantToken($token, $cart->getRestaurant())) {
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
     * @Route("/remove_product.json", name="api_order_remove_product_from_cart")
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
        if ($cart === null || !$this->checkRestaurantToken($token, $cart->getRestaurant())) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        if ($this->orderSv->isLocked($cart)) {
            return $this->errorResponse(self::ORDER_LOCKED);
        }
        
        if (!$request->request->has('product_id')) {
            return $this->errorResponse(self::BAD_PARAMETER);
        }
        
        $productId = intval($request->request->get('product_id'));
        $extra = $request->request->get('extra', null);
        $cart = $this->orderSv->removeProductFromOrder($cart, $productId, $extra);
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('cart' => $cart->accept($apiVisistor)));
    }
    
    /**
     * POST /order/cart/validate.json
     * 
     * Create a cart and return it.
     * 
     * Parameters:
     * - <strong>restaurant_token</strong> The alphanumeric token of the 
     *                                     restaurant.
     * - <strong>user_token</strong>       The alphanumeric token of the 
     *                                     user.
     * @return JsonResponse
     * @Route("/validate.json", name="api_order_validate_cart")
     * @Method("POST")
     */
    public function validateCartAction(Request $request)
    {
        // get cart
        $cartId = $request->request->get('cart_id', null);
        if ($cartId !== null) {
            $em = $this->getDoctrine()->getManager();
            $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
            $cart = $orderRepo->find($cartId);
        }
        
        // check restaurant token and user token
        $restaurantToken = $request->request->get('restaurant_token', null);
        $clientToken = $request->request->get('client_token', null);
        if ($cart === null || 
                !$this->checkClientToken($clientToken) || 
                !$this->checkRestaurantToken($restaurantToken, $cart->getRestaurant())) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        if ($this->orderSv->isLocked($cart)) {
            return $this->errorResponse(self::ORDER_LOCKED);
        }
        
        $deliveryDateParam = $request->request->get('delivery_date', null);
        $cart = $this->orderSv->validateCart($cart, $this->clientToken, $deliveryDateParam);
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('cart' => $cart->accept($apiVisistor)));
    }
}
