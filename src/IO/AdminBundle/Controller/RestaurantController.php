<?php

namespace IO\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\CarteBundle\Form\RestaurantType;
use IO\CarteBundle\Entity\Restaurant;

class RestaurantController extends Controller
{
    /**
     * Admin restaurant index
     * 
     * @return type
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        $restaurants = $this->getDoctrine()->getRepository("IOCarteBundle:Restaurant")->findAll();
        return array('restaurants' => $restaurants);
    }
    
    /**
     * Admin restaurant index
     * 
     * @return type
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction(Request $request)
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(new RestaurantType(), $restaurant);
        
        if ($request->isMethod("POST")) {
            $form->bind($request);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($restaurant);
            $em->flush();
            
            $session = $this->container->get('session');
            $session->getFlashBag()->add('success', 'Le restaurant à bien été crée.');
            
            return $this->redirect($this->generateUrl('admin_restaurants'));
        }
        
        return array('form' => $form->createView());
    }
}
