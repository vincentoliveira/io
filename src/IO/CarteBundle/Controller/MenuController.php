<?php

namespace IO\CarteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use IO\CarteBundle\Entity\Menu;
use IO\CarteBundle\Form\MenuType;
use IO\UserBundle\Service\UserService;

/**
 * Menu controller.
 *
 */
class MenuController extends Controller
{

    /**
     * Displays a form to create a new Menu entity.
     *
     * @Template("IOCarteBundle:Menu:new.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function newAction()
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();

        $entity = new Menu();
        $entity->setRestaurant($user->getRestaurant());
        $form = $this->createCreateForm($entity);

        return $this->render('IOCarteBundle:Menu:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }

    /**
     * Creates a new Menu entity.
     *
     * @Template("IOCarteBundle:Menu:new.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function createAction(Request $request)
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();

        $entity = new Menu();
        $entity->setRestaurant($user->getRestaurant());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('menu_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Menu entity.
     *
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IOCarteBundle:Menu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Menu entity.
     *
     * @Template("IOCarteBundle:Menu:edit.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function editAction($id)
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IOCarteBundle:Menu')->find($id);
        $entity->setRestaurant($user->getRestaurant());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Menu entity.
     *
     * @Template("IOCarteBundle:Menu:edit.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function updateAction(Request $request, $id)
    {
        $userSv = new UserService($this->container);
        $user = $userSv->getUser();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IOCarteBundle:Menu')->find($id);
        $entity->setRestaurant($user->getRestaurant());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('menu_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Menu entity.
     *
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('IOCarteBundle:Menu')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Menu entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('menu'));
    }

    /**
     * Creates a form to create a Menu entity.
     *
     * @param Menu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Menu $entity)
    {
        $form = $this->createForm(new MenuType(), $entity, array(
            'action' => $this->generateUrl('menu_create'),
            'method' => 'POST',
                ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Creates a form to edit a Menu entity.
     *
     * @param Menu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Menu $entity)
    {
        $form = $this->createForm(new MenuType(), $entity, array(
            'action' => $this->generateUrl('menu_update', array('id' => $entity->getId())),
            'method' => 'PUT',
                ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Creates a form to delete a Menu entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('menu_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
