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
        
        $repo = $this->getDoctrine()->getRepository('IOCarteBundle:Category');
        $categories = $repo->getRestaurantFinalCategory($user->getRestaurant()->getId());

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'categories' => $categories,
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
            
            foreach ($entity->getOrderLines() as $orderLine) {
                $orderLine->setOrder($entity);
                $orderLine->setItemPrice($orderLine->getDish()->getPrice());
                $em->persist($orderLine);
            }
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('commande_en_cours'));
        }
        
        $repo = $this->getDoctrine()->getRepository('IOCarteBundle:Category');
        $categories = $repo->getRestaurantFinalCategory($user->getRestaurant()->getId());

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'categories' => $categories,
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
        $userSv = $this->get('user.user_service');
        $user = $userSv->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IOOrderBundle:Order')->find($id);

        if (!$entity || $entity->getRestaurant() !== $user->getRestaurant()) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $editForm = $this->createEditForm($entity);
        
        $repo = $this->getDoctrine()->getRepository('IOCarteBundle:Category');
        $categories = $repo->getRestaurantFinalCategory($user->getRestaurant()->getId());

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'categories' => $categories,
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
        $userSv = $this->get('user.user_service');
        $user = $userSv->getUser();
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IOOrderBundle:Order')->find($id);

        if (!$entity || $entity->getRestaurant() !== $user->getRestaurant()) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $originalDishes = array();
        foreach ($entity->getOrderLines() as $tag) {
            $originalDishes[] = $tag;
        }

        $editForm = $this->createEditForm($entity);
        $editForm->bind($request);
        if ($editForm->isValid()) {
            
            // filtre $originalDishes pour ne contenir que les plats
            // n'étant plus présents
            foreach ($entity->getOrderLines() as $dish) {
                foreach ($originalDishes as $key => $toDel) {
                    if ($toDel->getId() === $dish->getId()) {
                        unset($originalDishes[$key]);
                        break;
                    }
                }
            }

            // supprime la relation entre les plat annulés
            foreach ($originalDishes as $dish) {
                $dish->getOrder()->removeOrderLine($dish);
                $em->persist($dish);
                $em->remove($dish);
            }
            
            // persist les plats commandés
            foreach ($entity->getOrderLines() as $orderLine) {
                $orderLine->setOrder($entity);
                $orderLine->setItemPrice($orderLine->getDish()->getPrice());
                $em->persist($orderLine);
            }
            
            $em->flush();

            return $this->redirect($this->generateUrl('commande_en_cours'));
        }
        
        $repo = $this->getDoctrine()->getRepository('IOCarteBundle:Category');
        $categories = $repo->getRestaurantFinalCategory($user->getRestaurant()->getId());

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'categories' => $categories,
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
            'em' => $this->getDoctrine()->getManager(),
            'action' => $this->generateUrl('order_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Envoyer en cuisine',
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
            'em' => $this->getDoctrine()->getManager(),
            'action' => $this->generateUrl('order_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Modifier la commande',
            'attr' => array('class' => 'btn btn-success'),
        ));

        return $form;
    }

}
