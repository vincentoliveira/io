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
class PaymentController extends DefaultController
{

    /**
     * Payment Service
     * 
     * @Inject("io.payment_service")
     * @var \IO\OrderBundle\Service\PaymentService
     */
    public $paymentSv;
    
    
    /**
     * POST /order/payment.json
     * 
     * Add product to a desired cart and return new cart
     * 
     * Parameters:
     * - <strong>token</strong>         The alphanumeric token of the 
     *                                  user/platform.
     * - <strong>order_id</strong>      The numerical id of the desired 
     *                                  cart
     * 
     * @return JsonResponse
     * @Route("/payment.json", name="api_payment")
     * @Method("POST")
     */
    public function paymentAction(Request $request)
    {
        $order = null;
        $orderId = $request->request->get('order_id', null);
        if ($orderId !== null) {
            $em = $this->getDoctrine()->getManager();
            $orderRepo = $em->getRepository("IOOrderBundle:OrderData");
            $order = $orderRepo->find($orderId);
        }
        
        // check restaurant token and user token
        $restaurantToken = $request->request->get('restaurant_token', null);
        if ($order === null || 
                !$this->checkRestaurantToken($restaurantToken, $order->getRestaurant())) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $payment = $this->paymentSv->handlePayment($request->request->all());
        if ($payment === null) {
            return $this->errorResponse(self::INTERNAL_ERROR);
        }

        $payment->setOrder($order);
        $order->addOrderPayment($payment);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($payment);
        $em->persist($order);
        $em->flush();
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('order' => $order->accept($apiVisistor)));
    }
    
}
