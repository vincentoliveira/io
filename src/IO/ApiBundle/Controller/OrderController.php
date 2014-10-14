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
     * POST /order/create.json
     * 
     * Create new order
     * 
     * Parameters:
     * - <strong>restaurant_token</strong>  The alphanumeric token of the 
     *                                      manager/platform.
     * - <strong>restaurant_id</strong>     The numerical id of the restaurant
     * - <strong>products</strong>          Array
     *      - product_id                    The numeric id of the desired product
     *      - options                       Array of option id
     * - <strong>source</strong>            Source
     * 
     * @return JsonResponse
     * @Route("/create.json", name="api_order_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // check token
        $token = $request->query->get('token', null);
        $restaurantId = $request->query->get('restaurant_id', null);
        $restaurant = null;
        if ($restaurantId) {
            $restaurantRepo = $em->getRepository("IORestaurantBundle:Restaurant");
            $restaurant = $restaurantRepo->find($restaurantId);
        }

        if (!$this->checkRestaurantToken($token, $restaurant)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }

        if ($restaurant === null) {
            $restaurant = $this->authToken->getRestaurant();
            if ($restaurant === null) {
                return $this->errorResponse(self::MISSING_PARAMETER, "Missing parameter: restaurant_id");
            }
        }
        
        $products = $request->request->get('products');
        $source = $request->request->get('source');
        
        if (!$products || empty($products)) {
            return $this->errorResponse(self::MISSING_PARAMETER, "Missing parameter: products");
        }
        
        $order = $this->orderSv->createOrder($restaurant, $this->authToken, $source);
        foreach ($products as $product) {
            if (isset($product['product_id']) && isset($product['options'])) {
                $productId = $product['product_id'];
                $options = $product['options'];
                $order = $this->orderSv->addProductToOrder($order, $productId, $options);
            }
        }
        
        if ($order->getOrderLines()->isEmpty()) {
            $em->remove($order);
            $em->flush();
            return $this->errorResponse(self::MISSING_PARAMETER, "Missing parameter: products");
        }
                        
        $order = $this->orderSv->validateCart($order);

        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array(
            'order' => $order->accept($apiVisistor),
        ));
    }


    /**
     * GET /order/current.json
     * 
     * Get all order of a restaurant
     * 
     * Parameters:
     * - <strong>token</strong>         The alphanumeric token of the 
     *                                  manager/platform.
     * - <strong>restaurant_id</strong> The numerical id of the restaurant
     * 
     * @return JsonResponse
     * @Route("/current.json", name="api_order_get_current")
     * @Method("GET")
     */
    public function getCurrentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // check token
        $token = $request->query->get('token', null);
        $restaurantId = $request->query->get('restaurant_id', null);
        $restaurant = null;
        if ($restaurantId) {
            $restaurantRepo = $em->getRepository("IORestaurantBundle:Restaurant");
            $restaurant = $restaurantRepo->find($restaurantId);
        }

        if (!$this->checkRestaurantToken($token, $restaurant)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }

        if ($restaurant === null) {
            $restaurant = $this->authToken->getRestaurant();
            if ($restaurant === null) {
                return $this->errorResponse(self::MISSING_PARAMETER, "Missing parameter: restaurant_id");
            }
        }
        
        $orders = $this->orderSv->getCurrentOrders($restaurant);
        $orderResults = array();
        $apiVisistor = new ApiElementVisitor();
        foreach ($orders as $order) {
            $orderResults[] = $order->accept($apiVisistor);
        }
        return new JsonResponse(array(
            'orders' => $orderResults,
            'restaurant' => $restaurant->accept($apiVisistor),
        ));
    }


    /**
     * PUT /order/{order-id}/cancel.json
     * 
     * Cancel an order
     * 
     * Parameters:
     * - <strong>restaurant_token</strong>         The alphanumeric token of the 
     *                                  manager/platform.
     * - <strong>restaurant_id</strong> The numerical id of the restaurant
     * 
     * @return JsonResponse
     * @Route("/{id}/cancel.json", name="api_order_cancel")
     * @Method("PUT")
     */
    public function cancelAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        // check token
        $token = $request->request->get('restaurant_token', null);
        $restaurantId = $request->request->get('restaurant_id', null);
        $restaurant = null;
        if ($restaurantId) {
            $restaurantRepo = $em->getRepository("IORestaurantBundle:Restaurant");
            $restaurant = $restaurantRepo->find($restaurantId);
        }
        
        if (!$this->checkRestaurantToken($token, $restaurant)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        if ($restaurant === null) {
            $restaurant = $this->authToken->getRestaurant();
            if ($restaurant === null) {
                return $this->errorResponse(self::MISSING_PARAMETER, "Missing parameter: restaurant_id");
            }
        }
        
        // check order
        $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
        $order = $orderRepo->findOneBy(array(
            'id' => $id,
            'restaurant' => $restaurant,
        ));
        
        if ($order === null) {
            return $this->errorResponse(self::UNKNOWN_ORDER);
        }
        
        if ($this->orderSv->isClosed($order)) {
            return $this->errorResponse(self::ORDER_LOCKED);
        }
        
        $this->orderSv->cancelOrder($order);
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array(
            'order' => $order->accept($apiVisistor),
        ));
    }
    
    
    /**
     * PUT /order/{order-id}/cancel.json
     * 
     * Set an order to its next status
     * 
     * Parameters:
     * - <strong>restaurant_token</strong>         The alphanumeric token of the 
     *                                  manager/platform.
     * - <strong>restaurant_id</strong> The numerical id of the restaurant
     * 
     * @return JsonResponse
     * @Route("/{id}/next_status.json", name="api_order_next_status")
     * @Method("PUT")
     */
    public function nextStatusAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        // check token
        $token = $request->request->get('restaurant_token', null);
        $restaurantId = $request->request->get('restaurant_id', null);
        $restaurant = null;
        if ($restaurantId) {
            $restaurantRepo = $em->getRepository("IORestaurantBundle:Restaurant");
            $restaurant = $restaurantRepo->find($restaurantId);
        }
        
        if (!$this->checkRestaurantToken($token, $restaurant)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        if ($restaurant === null) {
            $restaurant = $this->authToken->getRestaurant();
            if ($restaurant === null) {
                return $this->errorResponse(self::MISSING_PARAMETER, "Missing parameter: restaurant_id");
            }
        }
        
        // check order
        $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
        $order = $orderRepo->findOneBy(array(
            'id' => $id,
            'restaurant' => $restaurant,
        ));
        
        if ($order === null) {
            return $this->errorResponse(self::UNKNOWN_ORDER);
        }
        
        if ($this->orderSv->isClosed($order)) {
            return $this->errorResponse(self::ORDER_LOCKED);
        }
        
        $this->orderSv->setNextStatusToOrder($order);
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array(
            'order' => $order->accept($apiVisistor),
        ));
    }
}
