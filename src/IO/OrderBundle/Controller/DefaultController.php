<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\OrderBundle\Entity\Order;
use IO\OrderBundle\Enum\OrderStatusEnum;

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
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;
    
    /**
     * @Route("/", name="order_index")
     * @Template()
     * @Secure("ROLE_MANAGER")
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
     * @Route("/accept/{id}", name="order_accept")
     * @Secure("ROLE_MANAGER")
     */
    public function acceptAction($id)
    {
        $order = $this->getOrder($id);
        $order->setStatus(OrderStatusEnum::STATUS_IN_PROGRESS);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
        
        return $this->redirect($this->generateUrl('order_index'));
    }
    
    /**
     * @Route("/payed/{id}", name="order_payed")
     * @Secure("ROLE_MANAGER")
     */
    public function payedAction($id)
    {
        $order = $this->getOrder($id);
        $order->setStatus(OrderStatusEnum::STATUS_PAYED);
        $order->setPaymentDate(new \DateTime());
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();
        
        return $this->redirect($this->generateUrl('order_index'));
    }
    
    /**
     * 
     * @param integer $id
     * @return Order
     * @throws type
     */
    protected function getOrder($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IOOrderBundle:Order')->find($id);
        if (!$entity || $entity->getRestaurant()->getId() !== $this->userSv->getUserRestaurant()->getId()) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }
        
        return $entity;
    }
}
