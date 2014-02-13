<?php

namespace IO\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\UserBundle\Service\UserService;
use IO\MenuBundle\Form\DishType;
use IO\MenuBundle\Entity\Dish;

/**
 * Dish Controller
 */
class DishController extends Controller
{

    /**
     * Displays a form to create an new Dish entity.
     * 
     * @Template("IOMenuBundle:Dish:new.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function newAction()
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();
        
        $dish = new Dish();
        $dish->setRestaurant($user->getRestaurant());
        $form = $this->createForm(new DishType(), $dish);

        return array(
            'dish' => $dish,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing Dish entity.
     *
     * @Template("IOMenuBundle:Dish:new.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();
        
        $dish = new Dish();
        $dish->setRestaurant($user->getRestaurant());
        $form = $this->createForm(new DishType(), $dish);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dish);
            $em->flush();
            
            return $this->redirect($this->generateUrl('show_category', array('id' => $dish->getCategory()->getId())));
        }

        return array(
            'dish' => $dish,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Dish entity.
     * 
     * @Template("IOMenuBundle:Dish:edit.html.twig")
     * @ParamConverter("dish", class="IOMenuBundle:Dish", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     * 
     * @param \IO\MenuBundle\Entity\Dish $dish
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Dish $dish)
    {
        if ($this->userCanEditDish($dish) === false) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(DishType(), $dish);

        return array(
            'dish' => $dish,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing Dish entity.
     *
     * @Template("IOMenuBundle:Dish:edit.html.twig")
     * @ParamConverter("dish", class="IOMenuBundle:Dish", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \IO\MenuBundle\Entity\Dish $dish
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Dish $dish)
    {
        if ($this->userCanEditDish($dish) === false) {
            throw $this->createNotFoundException();
        }
        
        $form = $this->createForm(DishType(), $dish);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dish);
            $em->flush();
            
            return $this->redirect($this->generateUrl('show_category', array('id' => $dish->getCategory()->getId())));
        }

        return array(
            'dish' => $dish,
            'form' => $form->createView(),
        );
    }
    
    /**
     * User can edit dish ?
     * 
     * @param \IO\MenuBundle\Entity\Dish $dish
     * @return bolean
     */
    protected function userCanEditDish(Dish $dish)
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();
        return $user->hasRole('ROLE_ADMIN') || $dish->getRestaurant() === $user->getRestaurant();
    }

}