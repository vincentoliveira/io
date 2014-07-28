<?php

namespace IO\RestaurantBundle\Controller;

use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Doctrine\Common\Collections\ArrayCollection;
use IO\RestaurantBundle\Form\OptionType;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

/**
 * Option List (CarteItem) controller.
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
     * Displays all .
     *
     * @Route("/", name="option_index")
     * @Secure("ROLE_MANAGER")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('IORestaurantBundle:CarteItem');
        $lists = $repo->findBy(array(
            'restaurant' => $this->userSv->getCurrentRestaurant(),
            'itemType' => ItemTypeEnum::TYPE_OPTION_LIST,
        ));
        
        return array(
            'opt_lists' => $lists,
        );
    }
    
    
    /**
     * Displays a form to create a new CarteItem entity.
     *
     * @Route("/new", name="option_new")
     * @Secure("ROLE_MANAGER")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CarteItem();
        $entity->setRestaurant($this->userSv->getCurrentRestaurant());
        $entity->setItemType(ItemTypeEnum::TYPE_OPTION_LIST);
        $entity->setVisible(true);
        
        $option = new CarteItem();
        $option->setItemType(ItemTypeEnum::TYPE_OPTION);
        $option->setVisible(true);
        $entity->addChild($option);
        
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new OptionType(), $entity, array(
            'em' => $em,
        ));

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
        $restaurant = $this->userSv->getCurrentRestaurant();
        
        $entity = new CarteItem();
        $entity->setRestaurant($restaurant);
        $entity->setItemType(ItemTypeEnum::TYPE_OPTION_LIST);
        $entity->setVisible(true);

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new OptionType(), $entity, array(
            'em' => $em,
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            foreach ($entity->getChildren() as $option) {
                $option->setVisible(true);
                $option->setParent($entity);
                $option->setRestaurant($restaurant);
                $em->persist($option);
            }
            
            $em->persist($entity);
            $em->flush();

            $this->session->getFlashBag()->add('success', sprintf('L\'option "%s" a bien été ajoutée', $entity->getName()));
            return $this->redirect($this->generateUrl('option_index'));
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
        
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(new OptionType(), $entity, array(
            'em' => $em,
        ));

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
        
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(new OptionType(), $entity, array(
            'em' => $em,
        ));
        
        // Crée un tableau contenant les objets Tag courants de la
        // base de données
        $originalChoice = new ArrayCollection();
        foreach ($entity->getChildren() as $choice) {
            $originalChoice->add($choice);
        }
        
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            // supprime la relation entre le tag et la « Task »
            foreach ($originalChoice as $choice) {
                if ($entity->getChildren()->contains($choice) === false) {
                    $em->remove($choice);
                }
            }
            
            $restaurant = $this->userSv->getCurrentRestaurant();
            foreach ($entity->getChildren() as $option) {
                $option->setVisible(true);
                $option->setParent($entity);
                $option->setRestaurant($restaurant);
                $em->persist($option);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $this->session->getFlashBag()->add('success', sprintf('L\'option "%s" a bien été modifiée', $entity->getName()));
            return $this->redirect($this->generateUrl('option_index'));
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
        $this->session->getFlashBag()->add('success', sprintf('L\'option a bien été supprimée', $entity->getName()));

        return $this->redirect($this->generateUrl('option_index'));
    }
}
