<?php

namespace IO\RestaurantBundle\Controller;

use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\RestaurantBundle\Form\OptionType;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

/**
 * Option (CarteItem) controller.
 *
 * @Route("/option")
 */
class OptionController extends CarteItemController
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
     * Displays a form to create a new CarteItem entity.
     *
     * @Route("/new", name="option_new")
     * @Secure("ROLE_MANAGER")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        
        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getUserRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_OPTION);
        $entity->setVisible(true);
        
        $parentId = $request->query->get('parent', null);
        if ($parentId !== null) {
            $parent = $this->getEntity($parentId);
            $entity->setParent($parent);
        }
        
        $form = $this->createForm(new OptionType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }


    /**
     * Creates a new CarteItem entity.
     *
     * @Route("/create", name="option_create")
     * @Secure("ROLE_MANAGER")
     * @Method("POST")
     * @Template("IORestaurantBundle:Dish:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getUserRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_OPTION);

        $form = $this->createForm(new OptionType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->session->getFlashBag()->add('success', sprintf('L\'option "%s" a bien été ajoutée', $entity->getName()));
            return $this->redirect($this->generateUrl('option_list_index'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }


    /**
     * Displays a form to edit an existing CarteItem entity.
     *
     * @Route("/{id}/edit", name="option_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $editForm = $this->createForm(new OptionType(), $entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Edits an existing CarteItem entity.
     *
     * @Route("/{id}/update", name="option_update")
     * @Method("POST")
     * @Template("IORestaurantBundle:Dish:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $editForm = $this->createForm(new OptionType(), $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->session->getFlashBag()->add('success', sprintf('L\'option "%s" a bien été modifiée', $entity->getName()));
            return $this->redirect($this->generateUrl('option_list_index'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Deletes a CarteItem entity.
     *
     * @Route("/{id}/delete", name="option_delete")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->session->getFlashBag()->add('success', sprintf('L\'option "%s" a bien été supprimée', $entity->getName()));

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * Change visibility
     *
     * @Route("/{id}/visibility/{visibility}", name="option_visibility")
     * @Template()
     */
    public function visibilityAction($id, $visibility)
    {
        $entity = $this->getEntity($id);
        
        $em = $this->getDoctrine()->getManager();
        $entity->setVisible($visibility);
        $em->persist($entity);
        $em->flush();
        
        return $this->redirect($this->generateUrl('option_list_index'));
    }

}
