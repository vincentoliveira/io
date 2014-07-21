<?php

namespace IO\RestaurantBundle\Controller;

use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\RestaurantBundle\Form\DishType;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Enum\ItemTypeEnum;
use IO\RestaurantBundle\Form\DishOptionListType;
use IO\RestaurantBundle\Entity\DishOptionList;

/**
 * CarteItem controller.
 *
 * @Route("/dish")
 */
class DishController extends CarteItemController {

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
     * @var \IO\UserBundle\Service\MediaService
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
     * @Route("/new", name="dish_new")
     * @Secure("ROLE_MANAGER")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request) {

        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getCurrentRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_DISH);
        $entity->setVat(10.0);
        $entity->setVisible(true);

        $parentId = $request->query->get('parent', null);
        if ($parentId !== null) {
            $parent = $this->getEntity($parentId);
            $entity->setParent($parent);
            if ($parent->getVat() !== null) {
                $entity->setVat($parent->getVat());
            }
        }

        $form = $this->createForm(new DishType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new CarteItem entity.
     *
     * @Route("/create", name="dish_create")
     * @Secure("ROLE_MANAGER")
     * @Method("POST")
     * @Template("IORestaurantBundle:Dish:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getCurrentRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_DISH);

        $form = $this->createForm(new DishType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->mediaSv->handleMedia($entity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->session->getFlashBag()->add('success', sprintf('Le plat "%s" a bien été ajouté', $entity->getName()));
            return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getParent()->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CarteItem entity.
     *
     * @Route("/{id}/edit", name="dish_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
        $entity = $this->getEntity($id);
        $editForm = $this->createForm(new DishType(), $entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing CarteItem entity.
     *
     * @Route("/{id}/update", name="dish_update")
     * @Method("POST")
     * @Template("IORestaurantBundle:Dish:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $entity = $this->getEntity($id);
        $editForm = $this->createForm(new DishType(), $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $media = $this->mediaSv->handleMedia($entity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->session->getFlashBag()->add('success', sprintf('Le plat "%s" a bien été modifié', $entity->getName()));
            return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getParent()->getId())));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a CarteItem entity.
     *
     * @Route("/{id}/delete", name="dish_delete")
     */
    public function deleteAction($id) {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->session->getFlashBag()->add('success', sprintf('Le plat "%s" a bien été supprimé', $entity->getName()));

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * Change visibility
     *
     * @Route("/{id}/visibility/{visibility}", name="dish_visibility")
     * @Template()
     */
    public function visibilityAction($id, $visibility) {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $entity->setVisible($visibility);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getParent()->getId())));
    }

    /**
     * Displays a form to edit an existing CarteItem entity.
     *
     * @Route("/{id}/option/add", name="dish_add_option")
     * @Template()
     */
    public function addOptionAction(Request $request, $id) {
        $dish = $this->getEntity($id);

        $dishOption = new DishOptionList();
        $dishOption->setDish($dish);

        $formType = new DishOptionListType($this->userSv->getCurrentRestaurant());
        $form = $this->createForm($formType, $dishOption);

        if ($request->isMethod("POST")) {
            $form->submit($request);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($dishOption);
            $em->flush();

            return $this->redirect($this->generateUrl('dish_edit', array('id' => $dish->getId())));
        }

        return array(
            'entity' => $dish,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CarteItem entity.
     *
     * @Route("/option/delete/{id}", name="dish_delete_option")
     * @Template()
     */
    public function deleteOptionAction($id) {
        $entity = $this->getEntity($id, 'IORestaurantBundle:DishOptionList');
        $dish = $entity->getDish();

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('dish_edit', array('id' => $dish->getId())));
    }

}
