<?php

namespace IO\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\OrderBundle\Entity\Order;
use IO\OrderBundle\Form\OrderType;

/**
 * Order controller.
 *
 */
class OrderController extends Controller
{

    /**
     * Finds and displays a Order entity.
     *
     * @Template()
     * @Secure(roles="ROLE_CUISINIER, ROLE_SERVEUR")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IOOrderBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new Order entity.
     *
     * @Template()
     * @Secure(roles="ROLE_CUISINIER, ROLE_SERVEUR")
     */
    public function newAction()
    {
        $userSv = $this->get('user.user_service');
        $user = $userSv->getUser();
        
        $entity = new Order();
        $entity->setRestaurant($user->getRestaurant());
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Order entity.
     *
     * @Template("IOOrderBundle:Order:new.html.twig")
     * @Secure(roles="ROLE_CUISINIER, ROLE_SERVEUR")
     */
    public function createAction(Request $request)
    {
        $userSv = $this->get('user.user_service');
        $user = $userSv->getUser();
        
        $entity = new Order();
        $entity->setRestaurant($user->getRestaurant());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('commande_en_cours'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Order entity.
     *
     *
     * @Template("IOOrderBundle:Order:edit.html.twig")
     * @Secure(roles="ROLE_CUISINIER, ROLE_SERVEUR")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IOOrderBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Order entity.
     *
     *
     * @Template()
     * @Secure(roles="ROLE_CUISINIER, ROLE_SERVEUR")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IOOrderBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('commande_en_cours'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to create a Order entity.
     *
     * @param Order $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Order $entity)
    {
        $form = $this->createForm(new OrderType(), $entity, array(
            'action' => $this->generateUrl('order_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Ajouter',
            'attr' => array('class' => 'btn btn-success'),
        ));

        return $form;
    }

    /**
     * Creates a form to edit a Order entity.
     *
     * @param Order $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Order $entity)
    {
        $form = $this->createForm(new OrderType(), $entity, array(
            'action' => $this->generateUrl('order_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Modifier',
            'attr' => array('class' => 'btn btn-success'),
        ));

        return $form;
    }

}
