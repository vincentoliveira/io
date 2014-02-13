<?php

namespace IO\MenuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\UserBundle\Service\UserService;
use IO\MenuBundle\Form\CategoryType;
use IO\MenuBundle\Entity\Category;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{

    /**
     * Finds and displays a Category entity.
     *
     * @Template()
     * @ParamConverter("category", class="IOMenuBundle:Category", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function showAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $dishes = $em->getRepository('IOMenuBundle:Dish')->findBy(array('category' => $category));

        $deleteForm = $this->createDeleteForm($category->getId());

        return array(
            'category' => $category,
            'dishes' => $dishes,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Category entity.
     * 
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function newAction()
    {
        $userSv = new UserService($this->container);
        $entity = new Category();
        $entity->setRestaurant($userSv->getUserRestaurant());
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Category entity.
     *
     * @Template("IOMenuBundle:Category:new.html.twig")
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function createAction(Request $request)
    {
        $userSv = new UserService($this->container);
        $entity = new Category();
        $entity->setRestaurant($userSv->getUserRestaurant());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setOrder(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('category_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IOMenuBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('IOMenuBundle:Category:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * Edits an existing Category entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IOMenuBundle:Category')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('category_edit', array('id' => $id)));
        }

        return $this->render('IOMenuBundle:Category:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
    }

    /**
     * Deletes a Category entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('IOMenuBundle:Category')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Category entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('carte'));
    }

    /**
     * Creates a form to create a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Category $entity)
    {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_create'),
            'attr' => array('class' => 'edit-form'),
            'method' => 'POST',
                ));

        $form->add('submit', 'submit', array(
            'label' => 'Créer',
            'attr' => array('class' => 'btn btn-success'),
        ));

        return $form;
    }

    /**
     * Creates a form to edit a Category entity.
     *
     * @param Category $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Category $entity)
    {
        $form = $this->createForm(new CategoryType(), $entity, array(
            'action' => $this->generateUrl('category_update', array('id' => $entity->getId())),
            'attr' => array('class' => 'edit-form'),
            'method' => 'PUT',
                ));

        $form->add('submit', 'submit', array(
            'label' => 'Modifier',
            'attr' => array('class' => 'btn btn-success'),
        ));

        return $form;
    }

    /**
     * Creates a form to delete a Category entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('category_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array(
                            'label' => 'Supprimer',
                            'attr' => array('class' => 'btn btn-danger'),
                        ))
                        ->getForm();
    }

}
