<?php

namespace IO\RestaurantBundle\Controller;

use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\RestaurantBundle\Form\CategoryType;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

/**
 * CarteItem controller.
 *
 * @Route("/category")
 */
class CategoryController extends CarteItemController
{

    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;

    /**
     * User Service
     * 
     * @Inject("io.media_service")
     * @var \IO\RestaurantBundle\Service\MediaService
     */
    public $mediaSv;
    
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
     * @Route("/new", name="category_new")
     * @Secure("ROLE_MANAGER")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getCurrentRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_CATEGORY);
        
        $parentId = $request->query->get('parent', null);
        if ($parentId !== null) {
            $parent = $this->getEntity($parentId);
            $entity->setParent($parent);
        }
        
        $form = $this->createForm(new CategoryType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }


    /**
     * Creates a new CarteItem entity.
     *
     * @Route("/create", name="category_create")
     * @Secure("ROLE_MANAGER")
     * @Method("POST")
     * @Template("IORestaurantBundle:Category:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getCurrentRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_CATEGORY);

        $form = $this->createForm(new CategoryType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->mediaSv->handleMedia($entity);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->session->getFlashBag()->add('success', sprintf('La categorie "%s" a bien été ajoutée', $entity->getName()));
            return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }


    /**
     * Finds and displays a CarteItem entity.
     *
     * @Route("/{id}", name="category_show")
     * @Secure("ROLE_MANAGER")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $entity = $this->getEntity($id);

        return array(
            'entity' => $entity,
        );
    }


    /**
     * Displays a form to edit an existing CarteItem entity.
     *
     * @Route("/{id}/edit", name="category_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $editForm = $this->createForm(new CategoryType(), $entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Edits an existing CarteItem entity.
     *
     * @Route("/{id}/update", name="category_update")
     * @Method("POST")
     * @Template("IORestaurantBundle:Category:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $editForm = $this->createForm(new CategoryType(), $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $media = $this->mediaSv->handleMedia($entity);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->session->getFlashBag()->add('success', sprintf('La categorie "%s" a bien été modifiée', $entity->getName()));
            return $this->redirect($this->generateUrl('category_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Deletes a CarteItem entity.
     *
     * @Route("/{id}/delete", name="category_delete")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->session->getFlashBag()->add('success', sprintf('La categorie "%s" a bien été supprimée', $entity->getName()));

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * Change visibility
     *
     * @Route("/{id}/visibility/{visibility}", name="category_visibility")
     * @Template()
     */
    public function visibilityAction($id, $visibility)
    {
        $entity = $this->getEntity($id);
        
        $em = $this->getDoctrine()->getManager();
        $entity->setVisible($visibility);
        $em->persist($entity);
        $em->flush();
        
        return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getId())));
    }

}
