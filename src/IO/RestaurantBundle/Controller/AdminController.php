<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\RestaurantBundle\Form\RestaurantGroupType;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\RestaurantBundle\Entity\RestaurantGroup;


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
        $restaurants = $this->getDoctrine()->getRepository("IORestaurantBundle:RestaurantGroup")->findAll();
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
        $group = new RestaurantGroup();
        $form = $this->createForm(new RestaurantGroupType(), $group);
        
        if ($request->isMethod("POST")) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $restaurant = new Restaurant();
                $restaurant->setName($group->getName());
                $restaurant->setGroup($group);
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($group);
                $em->persist($restaurant);
                $em->flush();

                $session = $this->container->get('session');
                $session->getFlashBag()->add('success', sprintf('Le restaurant "%s" a bien été ajouté.', $group->getName()));

                return $this->redirect($this->generateUrl('admin_restaurant_index'));
            }
        }
        
        return array('form' => $form->createView());
    }
}
