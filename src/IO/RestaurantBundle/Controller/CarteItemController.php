<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Form\CarteItemType;

/**
 * CarteItem abstract controller.
 */
abstract class CarteItemController extends Controller
{

    /**
    * Creates a form to create a CarteItem entity.
    *
    * @param CarteItem $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    protected function createCreateForm(CarteItem $entity)
    {
        $form = $this->createForm(new CarteItemType(), $entity);

        return $form;
    }
    
    /**
    * Creates a form to edit a CarteItem entity.
    *
    * @param CarteItem $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    protected function createEditForm(CarteItem $entity)
    {
        $form = $this->createForm(new CarteItemType(), $entity);

        return $form;
    }
}
