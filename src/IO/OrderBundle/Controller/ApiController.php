<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;

/**
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
     * @Inject("io.order_service")
     * @var \IO\OrderBundle\Service\OrderService
     */
    public $orderSv;
    
    /**
     * @Route("/order.json", name="order_api_order")
     * @Method("POST")
     */
    public function orderAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        if ($data === null || !is_array($data)) {
            return new JsonResponse(array('error' => 'Bad data'));
        }
        if (empty($data)) {
            return new JsonResponse(array('error' => 'Empty command'));
        }
        
        $restaurant = $this->userSv->getUserRestaurant();
        $order = $this->orderSv->processOrder($data, $restaurant);
        
        $response = array(
            'order' => array(
                'id' => $order->getId(),
                'status' => $order->getStatus(),
                'total_price' => $order->getTotalPrice(),
            ),
        );
        return new JsonResponse($response);
    }
}
