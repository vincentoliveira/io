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
        $form = $this->createCreateForm($dish);

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
        $form = $this->createCreateForm($dish);
        $form->bind($request);
        
        $session = $this->container->get('session');
        if ($form->isValid()) {
            $mediaSv = $this->container->get('menu.media');
            $file = $dish->getFile();
            if ($mediaSv->isFileValid($file)) {
                if ($file !== null) {
                    $media = $mediaSv->createMediaFromFile($file);
                    $dish->setMedia($media);
                } 
                
                $dish->setOrder(0);
                $em = $this->getDoctrine()->getManager();
                $em->persist($dish);
                $em->flush();

                $session->getFlashBag()->add('success', 'Le plat à bien été ajouté');
                return $this->redirect($this->generateUrl('category_show', array('id' => $dish->getCategory()->getId())));
            }
    
            $session->getFlashBag()->add('error', "Image non valide");
        } else {
            $formErrorSv = $this->container->get('menu.form_error');
            $errors = $formErrorSv->getAllFormErrorMessages($form);
            foreach ($errors as $error) {
                $session->getFlashBag()->add('error', $error);
            }
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

        $form = $this->createEditForm($dish);
        $deleteForm = $this->createDeleteForm($dish->getId());

        return array(
            'dish' => $dish,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
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
        
        $form = $this->createEditForm($dish);
        $form->bind($request);
        
        $session = $this->container->get('session');
        if ($form->isValid()) {
            $mediaSv = $this->container->get('menu.media');
            $file = $dish->getFile();
            if ($mediaSv->isFileValid($file)) {
                if ($file !== null) {
                    $media = $mediaSv->createMediaFromFile($file);
                    $dish->setMedia($media);
                } 
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($dish);
                $em->flush();

                $session->getFlashBag()->add('success', 'Le plat à bien été modifié');
                return $this->redirect($this->generateUrl('category_show', array('id' => $dish->getCategory()->getId())));
            }
    
            $session->getFlashBag()->add('error', "Image non valide");
        } else {
            $formErrorSv = $this->container->get('menu.form_error');
            $errors = $formErrorSv->getAllFormErrorMessages($form);
            foreach ($errors as $error) {
                $session->getFlashBag()->add('error', $error);
            }
        }
        
        $deleteForm = $this->createDeleteForm($dish->getId());

        return array(
            'dish' => $dish,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Category entity.
     *
     * @Template()
     * @ParamConverter("dish", class="IOMenuBundle:Dish", options={"id" = "id"})
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function deleteAction(Request $request, Dish $dish)
    {
        if ($this->userCanEditDish($dish) === false) {
            throw $this->createNotFoundException();
        }
        
        $form = $this->createDeleteForm($dish->getId());
        $form->handleRequest($request);
        
        $category = $dish->getCategory();
        
        $session = $this->container->get('session');
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dish);
            $em->flush();
            
            $session->getFlashBag()->add('success', 'Le plat à bien été supprimé');
        }

        if ($category !== null) {
            return $this->redirect($this->generateUrl('category_show', array('id' => $category->getId())));
        } else {
            return $this->redirect($this->generateUrl('carte'));
        }
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

    /**
     * Creates a form to create a Category entity.
     *
     * @param Dish $dish The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Dish $dish)
    {
        $form = $this->createForm(new DishType(), $dish, array(
            'action' => $this->generateUrl('dish_create'),
            'attr' => array('class' => 'menu-form'),
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
     * @param Dish $dish The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Dish $dish)
    {
        $form = $this->createForm(new DishType(), $dish, array(
            'action' => $this->generateUrl('dish_update', array('id' => $dish->getId())),
            'attr' => array('class' => 'menu-form'),
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
                        ->setAction($this->generateUrl('dish_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array(
                            'label' => 'Supprimer',
                            'attr' => array('class' => 'btn btn-danger btn-delete'),
                        ))
                        ->getForm();
    }

}