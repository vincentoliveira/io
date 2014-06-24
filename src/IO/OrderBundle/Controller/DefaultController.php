<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\OrderBundle\Entity\OrderData;
use IO\OrderBundle\Entity\OrderPayment;
use IO\OrderBundle\Entity\OrderStatus;
use IO\OrderBundle\Enum\OrderStatusEnum;
use IO\OrderBundle\Enum\PaymentTypeEnum;
use IO\OrderBundle\Enum\PaymentStatusEnum;

/**
 * @Route("/order")
 */
class DefaultController extends Controller
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
     * @Route("/", name="order_index")
     * @Template()
     * @Secure("ROLE_EMPLOYEE")
     */
    public function indexAction()
    {
        $restaurant = $this->userSv->getUserRestaurant();
        $orders = $this->orderSv->getCurrentOrders($restaurant);
        return array(
            'orders' => $orders,
        );
    }
    
    /**
     * @Route("/refresh", name="order_refresh")
     * @Template("IOOrderBundle:Default:order_list.html.twig")
     * @Method("POST")
     * @Secure("ROLE_EMPLOYEE")
     */
    public function indexRefreshCallAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $restaurantId = $request->request->get('restaurant_id');
        if ($restaurantId === null) {
            throw $this->createNotFoundException('Unable to find Restaurant entity.');
        }
        
        $restaurant = $em->getRepository('IORestaurantBundle:Restaurant')->find($restaurantId);
        
        $orders = $this->orderSv->getCurrentOrders($restaurant);
        return array(
            'orders' => $orders,
        );
    }
    
    /**
     * @Route("/accept/{id}", name="order_accept")
     * @Secure("ROLE_EMPLOYEE")
     */
    public function acceptAction($id)
    {
        $order = $this->getOrder($id);
        
        $status = new OrderStatus();
        $status->setOrder($order);
        $status->setDate(new \DateTime());
        $status->setOldStatus($order->getLastStatus());
        $status->setNewStatus(OrderStatusEnum::STATUS_IN_PROGRESS);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($status);
        $em->flush();
        
        return $this->redirect($this->generateUrl('order_index'));
    }
    
    /**
     * @Route("/close/{id}", name="order_close")
     * @Secure("ROLE_EMPLOYEE")
     */
    public function closeAction($id)
    {
        $order = $this->getOrder($id);
        
        $status = new OrderStatus();
        $status->setOrder($order);
        $status->setDate(new \DateTime());
        $status->setOldStatus($order->getLastStatus());
        $status->setNewStatus(OrderStatusEnum::STATUS_CLOSED);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($status);
        $em->flush();
        
        return $this->redirect($this->generateUrl('order_index'));
    }
    
    /**
     * @Route("/cancel/{id}", name="order_cancel")
     * @Secure("ROLE_EMPLOYEE")
     */
    public function cancelAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $order = $this->getOrder($id);
        
        $status = new OrderStatus();
        $status->setOrder($order);
        $status->setDate(new \DateTime());
        $status->setOldStatus($order->getLastStatus());
        $status->setNewStatus(OrderStatusEnum::STATUS_CANCELED);
        
        foreach ($order->getOrderPayments() as $payment) {
            $payment->setStatus(PaymentStatusEnum::PAYMENT_CANCELED);
            $em->persist($payment);
        }
        
        $em->persist($status);
        $em->flush();
        
        return $this->redirect($this->generateUrl('order_index'));
    }
    
    /**
     * @Route("/payed/{id}", name="order_payed")
     * @Secure("ROLE_EMPLOYEE")
     */
    public function payedAction($id)
    {
        $order = $this->getOrder($id);
        
        $status = new OrderStatus();
        $status->setOrder($order);
        $status->setDate(new \DateTime());
        $status->setOldStatus($order->getLastStatus());
        $status->setNewStatus(OrderStatusEnum::STATUS_CLOSED);
        
        $payment = new OrderPayment();
        $payment->setOrder($order);
        $payment->setDate(new \DateTime());
        $payment->setAmount($order->getTotalPrice() - $order->getPayedAmount());
        $payment->setType(PaymentTypeEnum::PAYMENT_CASH);
        $payment->setStatus(PaymentStatusEnum::PAYMENT_SUCCESS);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($status);
        $em->persist($payment);
        $em->flush();
        
        return $this->redirect($this->generateUrl('order_index'));
    }
    
    /**
     * 
     * @param integer $id
     * @return OrderData
     * @throws type
     */
    protected function getOrder($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IOOrderBundle:OrderData')->find($id);
        if (!$entity || $entity->getRestaurant()->getId() !== $this->userSv->getUserRestaurant()->getId()) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }
        
        return $entity;
    }
}
