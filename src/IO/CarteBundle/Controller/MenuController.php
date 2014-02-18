<?php

namespace IO\CarteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\UserBundle\Service\UserService;
use IO\CarteBundle\Form\MenuType;
use IO\CarteBundle\Entity\Menu;

/**
 * Menu Controller
 */
class MenuController extends Controller
{

    /**
     * Displays a form to create a menu
     * 
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $userSv = new UserService($this->container);
        
        $menu = new Menu();
        $menu->setRestaurant($userSv->getUser()->getRestaurant());
        $form = $this->createForm(new MenuType(), $menu);

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing Dish entity.
     *
     * @Template("IOCarteBundle:Menu:new.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        $userSv = new UserService($this->container);
        
        $menu = new Menu();
        $menu->setRestaurant($userSv->getUser()->getRestaurant());
        $form = $this->createForm(new MenuType(), $menu);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            return $this->redirect($this->generateUrl('category_show', array('id' => $menu->getCategory()->getId())));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * User can edit menu ?
     * 
     * @param \IO\CarteBundle\Entity\Menu $menu
     * @return bolean
     */
    protected function userCanEditMenu(Menu $menu)
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();
        return $user->hasRole('ROLE_ADMIN') || $menu->getRestaurant() === $user->getRestaurant();
    }

}