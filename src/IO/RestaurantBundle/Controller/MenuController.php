<?php

namespace IO\RestaurantBundle\Controller;

use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\RestaurantBundle\Form\OptionListType;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

/**
 * Menu (CarteItem) controller.
 *
 * @Route("/menu")
 */
class MenuController extends CarteItemController
{

    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;
    
    /**
     * Session
     * 
     * @Inject("session")
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    public $session;

    
    /**
     * Displays all .
     *
     * @Route("/", name="menu_index")
     * @Secure("ROLE_MANAGER")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('IORestaurantBundle:CarteItem');
        $menus = $repo->findBy(array(
            'restaurant' => $this->userSv->getUserRestaurant(),
            'itemType' => ItemTypeEnum::TYPE_MENU,
        ));
        
        return array(
            'menus' => $menus,
        );
    }
    
    
    /**
     * Displays a form to create a new CarteItem entity.
     *
     * @Route("/new", name="menu_new")
     * @Secure("ROLE_MANAGER")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getUserRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_MENU);
        $entity->setVisible(true);
        
        $form = $this->createForm(new OptionListType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }


    /**
     * Creates a new CarteItem entity.
     *
     * @Route("/create", name="menu_create")
     * @Secure("ROLE_MANAGER")
     * @Method("POST")
     * @Template("IORestaurantBundle:Dish:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getUserRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_MENU);
        $entity->setVisible(true);

        $form = $this->createForm(new OptionListType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->session->getFlashBag()->add('success', sprintf('Le menu "%s" a bien été ajouté', $entity->getName()));
            return $this->redirect($this->generateUrl('menu_index'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }


    /**
     * Displays a form to edit an existing CarteItem entity.
     *
     * @Route("/{id}/edit", name="menu_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $editForm = $this->createForm(new OptionListType(), $entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Edits an existing CarteItem entity.
     *
     * @Route("/{id}/update", name="menu_update")
     * @Method("POST")
     * @Template("IORestaurantBundle:Dish:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $editForm = $this->createForm(new OptionListType(), $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->session->getFlashBag()->add('success', sprintf('Le menu "%s" a bien été modifié', $entity->getName()));
            return $this->redirect($this->generateUrl('menu_index'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Deletes a CarteItem entity.
     *
     * @Route("/{id}/delete", name="menu_delete")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->session->getFlashBag()->add('success', sprintf('Le menu a bien été supprimé', $entity->getName()));

        return $this->redirect($this->generateUrl('menu_index'));
    }
}
