<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\OrderBundle\Form\OrderDataType;

/**
 * @Route("/order/remote")
 */
class RemoteController extends Controller
{
    /**
     * CarteItem Service
     * 
     * @Inject("io.carte_item_service")
     * @var \IO\RestaurantBundle\Service\CarteItemService
     */
    public $carteItemSv;
    
    /**
     * CarteItem Service
     * 
     * @Inject("io.remote_order_service")
     * @var \IO\OrderBundle\Service\RemoteOrderService
     */
    public $remoteOrderSv;
    
    
    /**
     * Displays carte
     *
     * @Route("/{name}", name="remote_order_index")
     * @Template()
     */
    public function indexAction($name)
    {
        $restaurant = $this->getRestaurant($name);
        
        if ($restaurant === null) {
            throw $this->createNotFoundException('Unable to find Restaurant entity.');
        }
        
        $draftOrder = $this->remoteOrderSv->getCurrentDraftOrder($restaurant);
        
        $carte = $this->carteItemSv->getCarte($restaurant);
        return array(
            'restaurant' => $restaurant,
            'carte' => $carte,
            'draftOrder' => $draftOrder,
        );
    }
    
    
    /**
     * Add product to order
     *
     * @Route("/{name}/add/{id}", name="remote_order_add_product")
     */
    public function addProductToOrderAction($name, $id)
    {
        $restaurant = $this->getRestaurant($name);
        $product = $this->getProduct($id, $restaurant);
        $draftOrder = $this->remoteOrderSv->addProductToOrder($restaurant, $product);
        $this->remoteOrderSv->setCurrentDraftOrder($draftOrder);
        
        return $this->redirect($this->generateUrl('remote_order_index', array('name' => $name)));
    }
    
    
    /**
     * Add product to order
     *
     * @Route("/{name}/validate", name="remote_order_validate")
     * @Template()
     */
    public function validateAction($name, Request $request)
    {
        $restaurant = $this->getRestaurant($name);
        $draftOrder = $this->remoteOrderSv->getCurrentDraftOrder($restaurant);
        $form = $this->createForm(new OrderDataType(), $draftOrder);
        
        if ($request->isMethod('POST')) {
            $form->submit($request);
            
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                
                $customer = $draftOrder->getCustomer();
                $existingCustomer = $this->getDoctrine()->getRepository('IOOrderBundle:Customer')->findOneByEmail($customer->getEmail());
                if ($existingCustomer !== null) {
                    $draftOrder->setCustomer($existingCustomer);
                } else {
                    $em->persist($customer);
                }
                
                $em->persist($draftOrder);
                $em->flush();
                
                $this->remoteOrderSv->sendOrder($draftOrder);
                
                //return $this->redirect($this->generateUrl('remote_order_success', array('name' => $name, 'id' => $draftOrder->getId())));
            }
        }
        
        return array(
            'restaurant' => $restaurant,
            'draftOrder' => $draftOrder,
            'form' => $form->createView(),
        );
    }
    
    
    /**
     * Add product to order
     *
     * @Route("/{name}/success/{id}", name="remote_order_success")
     * @Template()
     */
    public function successAction($name, $id)
    {
        $restaurant = $this->getRestaurant($name);
        $draftOrder = $this->getDoctrine()->getRepository('IOOrderBundle:OrderData')->find($id);

        return array(
            'restaurant' => $restaurant,
            'draftOrder' => $draftOrder,
        );
    }
    
    
    
    /**
     * Get restaurant
     * 
     * @param string $name Restaurant name
     * @return \IO\RestaurantBundle\Entity\Restaurant
     * @throws type
     */
    protected function getRestaurant($name)
    {
        $repo = $this->getDoctrine()->getRepository('IORestaurantBundle:Restaurant');
        $restaurant = $repo->findOneByName($name);
        
        if ($restaurant === null) {
            throw $this->createNotFoundException('Unable to find Restaurant entity.');
        }
        
        return $restaurant;
    }
    
    
    /**
     * Get product
     * 
     * @param int $id
     * @param \IO\OrderBundle\Controller\Restaurant $restaurant
     * @return \IO\RestaurantBundle\Entity\CarteItem
     */
    protected function getProduct($id, Restaurant $restaurant)
    {
        $repo = $this->getDoctrine()->getRepository('IORestaurantBundle:CarteItem');
        return $repo->findOneBy(array(
            'id' => $id,
            'restaurant' => $restaurant,
        ));
    }
}
