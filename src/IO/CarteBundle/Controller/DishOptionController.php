<?php

namespace IO\CarteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\CarteBundle\Entity\DishOption;
use IO\CarteBundle\Form\DishOptionType;

/**
 * DishOption controller.
 *
 */
class DishOptionController extends Controller
{
    /**
     * Lists all DishOption entities.
     *
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function indexAction()
    {
        $userSv = $this->container->get('user.user_service');
        $service = $this->container->get('menu.dish_option');

        $options = $service->findAll($userSv->getUser()->getRestaurant()->getId());

        return array(
            'options' => $options,
        );
    }


    /**
     * Displays a form to create a new DishOption entity.
     *
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function newAction()
    {
        $entity = new DishOption();
        $form = $this->createCreateForm($entity);

        return $this->render('IOCarteBundle:DishOption:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }


    /**
     * Creates a new DishOption entity.
     *
     * @Template("IOCarteBundle:DishOption:new.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function createAction(Request $request)
    {
        $userSv = $this->container->get('user.user_service');

        $entity = new DishOption();
        $entity->setRestaurant($userSv->getUser()->getRestaurant());

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $session = $this->container->get('session');
            $session->getFlashBag()->add('success', 'L\'option à bien été ajoutée');

            return $this->redirect($this->generateUrl('options'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }


    /**
     * Displays a form to edit an existing DishOption entity.
     *
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $userSv = $this->container->get('user.user_service');
        $restaurantId = $userSv->getUser()->getRestaurant()->getId();

        $entity = $em->getRepository('IOCarteBundle:DishOption')->findWithRestaurant($id, $restaurantId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DishOption entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Edits an existing DishOption entity.
     *
     * @Template("IOCarteBundle:DishOption:edit.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $userSv = $this->container->get('user.user_service');
        $restaurantId = $userSv->getUser()->getRestaurant()->getId();

        $entity = $em->getRepository('IOCarteBundle:DishOption')->findWithRestaurant($id, $restaurantId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DishOption entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $session = $this->container->get('session');
            $session->getFlashBag()->add('success', 'L\'option à bien été modifiée');
            return $this->redirect($this->generateUrl('options'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }


    /**
     * Deletes a DishOption entity.
     * 
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $userSv = $this->container->get('user.user_service');
        $restaurantId = $userSv->getUser()->getRestaurant()->getId();

        $entity = $em->getRepository('IOCarteBundle:DishOption')->findWithRestaurant($id, $restaurantId);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DishOption entity.');
        }

        $session = $this->container->get('session');
        $session->getFlashBag()->add('success', 'L\'option à bien été supprimée');

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('options'));
    }


    /**
     * Creates a form to create a DishOption entity.
     *
     * @param DishOption $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(DishOption $entity)
    {
        $form = $this->createForm(new DishOptionType(), $entity, array(
            'action' => $this->generateUrl('options_create'),
            'method' => 'POST',
            'attr' => array('class' => 'menu-form'),
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Ajouter',
            'attr' => array('class' => 'btn btn-success'),
        ));

        return $form;
    }


    /**
     * Creates a form to edit a DishOption entity.
     *
     * @param DishOption $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(DishOption $entity)
    {
        $form = $this->createForm(new DishOptionType(), $entity, array(
            'action' => $this->generateUrl('options_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('class' => 'menu-form'),
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Valider',
            'attr' => array('class' => 'btn btn-success'),
        ));

        return $form;
    }


}
