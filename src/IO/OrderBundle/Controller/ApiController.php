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
        if ($data === null || !is_array($data) || empty($data)) {
            return new JsonResponse(array('error' => 'Empty command'));
        }
        
        $restaurant = $this->userSv->getUserRestaurant();
        $order = $this->orderSv->processOrder($data, $restaurant);
        
        $response = array(
            'order' => array(
                'id' => $order->getId(),
                'status' => $order->getLastStatus(),
                'total_price' => $order->getTotalPrice(),
            ),
        );
        return new JsonResponse($response);
    }

    /**
     * @Route("/order/{id}/payment.json", name="order_api_payment")
     * @Method("POST")
     */
    public function paymentAction(Request $request, $id)
    {
        $order = $this->getOrderData($id);
        if ($order === null) {
            return new JsonResponse(array('error' => 'You are not allowed to do this action'));
        }
        
        $data = json_decode($request->getContent(), true);
        if ($data === null || !is_array($data) || empty($data)) {
            return new JsonResponse(array('error' => 'Empty payment'));
        }
            
        $this->orderSv->processPayment($order, $data);
        $this->getDoctrine()->getEntityManager()->refresh($order);
        
        $response = array(
            'order' => array(
                'id' => $order->getId(),
                'status' => $order->getLastStatus(),
                'total_price' => $order->getTotalPrice(),
                'payed_amount' => $order->getPayedAmount(),
            ),
        );
        return new JsonResponse($response);
    }
    
    /**
     * Get Order data
     * 
     * @param integer $id
     * @return \IO\OrderBundle\Entity\OrderData
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getOrderData($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IOOrderBundle:OrderData')->find($id);
        if ($entity === null || $entity->getRestaurant()->getId() !== $this->userSv->getUserRestaurant()->getId()) {
            return null;
        }
        
        return $entity;
    }

}
