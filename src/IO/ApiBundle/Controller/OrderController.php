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
        
        $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
        $order = $orderRepo->findOneBy(array(
            'id' => $id,
            'restaurant' => $restaurant,
        ));
        
        if ($order === null) {
            return $this->errorResponse(self::UNKNOWN_ORDER);
        }
        
        $this->orderSv->cancelOrder($order);
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array(
            'order' => $order->accept($apiVisistor),
        ));
    }
}
