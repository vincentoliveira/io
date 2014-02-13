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
        $category = new Category();
        $category->setRestaurant($userSv->getUserRestaurant());
        $form = $this->createCreateForm($category);

        return array(
            'category' => $category,
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
        $category = new Category();
        $category->setRestaurant($userSv->getUserRestaurant());
        $form = $this->createCreateForm($category);
        $form->handleRequest($request);

        $session = $this->container->get('session');
        if ($form->isValid()) {
            $category->setOrder(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $session->getFlashBag()->add('success', 'La categorie à bien été ajoutée');
            return $this->redirect($this->generateUrl('category_show', array('id' => $category->getId())));
        }

        return array(
            'category' => $category,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Template("IOMenuBundle:Category:edit.html.twig")
     * @ParamConverter("category", class="IOMenuBundle:Category", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function editAction(Category $category)
    {
        $editForm = $this->createEditForm($category);

        return array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Category entity.
     *
     * @Template("IOMenuBundle:Category:edit.html.twig")
     * @ParamConverter("category", class="IOMenuBundle:Category", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function updateAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createEditForm($category);
        $editForm->handleRequest($request);

        $session = $this->container->get('session');
        if ($editForm->isValid()) {
            $em->persist($category);
            $em->flush();

            $session->getFlashBag()->add('success', 'La categorie à bien été modifiée');
            return $this->redirect($this->generateUrl('category_show', array('id' => $category->getId())));
        }

        return array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Category entity.
     *
     * @Template()
     * @ParamConverter("category", class="IOMenuBundle:Category", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function deleteAction(Request $request, Category $category)
    {
        $form = $this->createDeleteForm($category->getId());
        $form->handleRequest($request);
        
        $session = $this->container->get('session');
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            
            $session->getFlashBag()->add('success', 'La categorie à bien été supprimée');
        }

        return $this->redirect($this->generateUrl('carte'));
    }

    /**
     * Creates a form to create a Category entity.
     *
     * @param Category $category The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Category $category)
    {
        $form = $this->createForm(new CategoryType(), $category, array(
            'action' => $this->generateUrl('category_create'),
            'attr' => array('class' => 'edit-form'),
            'method' => 'POST',
                ));

        $form->add('submit', 'submit', array(
            'label' => 'Ajouter',
            'attr' => array('class' => 'btn btn-success'),
        ));

        return $form;
    }

    /**
     * Creates a form to edit a Category entity.
     *
     * @param Category $category The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Category $category)
    {
        $form = $this->createForm(new CategoryType(), $category, array(
            'action' => $this->generateUrl('category_update', array('id' => $category->getId())),
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
