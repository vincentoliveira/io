<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\RestaurantBundle\Form\RestaurantType;
use IO\RestaurantBundle\Entity\Restaurant;


/**
 * Admin User Controller
 * 
 * @Route("/admin/restaurant")
 */
class AdminController extends Controller
{
    /**
     * Admin restaurant index
     * 
     * @return type
     * @Route("/", name="admin_restaurant_index")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function indexAction()
    {
        $restaurants = $this->getDoctrine()->getRepository("IORestaurantBundle:Restaurant")->findAll();
        return array('restaurants' => $restaurants);
    }
    
    /**
     * Admin add restaurant
     * 
     * @return type
     * @Route("/new", name="admin_restaurant_new")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction(Request $request)
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(new RestaurantType(), $restaurant);
        
        if ($request->isMethod("POST")) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($restaurant);
                $em->flush();

                $session = $this->container->get('session');
                $session->getFlashBag()->add('success', sprintf('Le restaurant "%s" a bien été ajouté.', $restaurant->getName()));

                return $this->redirect($this->generateUrl('admin_restaurant_index'));
            }
        }
        
        return array('form' => $form->createView());
    }
}
