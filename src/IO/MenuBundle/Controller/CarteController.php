<?php

namespace IO\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;

use IO\UserBundle\Service\UserService;
use IO\MenuBundle\Form\SelectRestaurantType;

use IO\MenuBundle\Entity\Category;
use IO\MenuBundle\Entity\Restaurant;
use IO\MenuBundle\Entity\Dish;

/**
 * Carte Controller
 */
class CarteController extends Controller
{
    /**
     * Display menu
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function indexAction(Request $request)
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();
        if ($user->hasRole('ROLE_ADMIN') === false) {
            return $this->redirect($this->generateUrl('menu_show_carte', array('id' => $user->getRestaurant()->getId())));
        }
        
        $form = $this->createForm(new SelectRestaurantType());
        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                return $this->redirect($this->generateUrl('menu_show_carte', array('id' => $data['restaurant']->getId())));
            }
        }
        
        return array(
            'form' => $form->createView(),
        );
    }
    
    /**
     * Display menu
     * 
     * @param Restaurant $restaurant
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template("")
     * @ParamConverter("restaurant", class="IOMenuBundle:Restaurant", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function showCarteAction(Restaurant $restaurant)
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();
        if ($user->hasRole('ROLE_ADMIN') === false && $user->getRestaurant() !== $restaurant) {
            return $this->createNotFoundException('Vous ne pouvez pas voir ce menu.');
        }
        
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('IOMenuBundle:Category')
                ->findBy(array('restaurant' => $restaurant, 'parent' => null));
        
        return array(
            'categories' => $categories,
        );
    }
    
    
    /**
     * Display menu category
     * 
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @ParamConverter("category", class="IOMenuBundle:Category", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function showCategoryAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $dishes = $em->getRepository('IOMenuBundle:Dish')->findBy(array('category' => $category));
        
        return array(
            'category' => $category,
            'dishes' => $dishes,
        );
    }
}