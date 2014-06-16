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
     * Get Entity
     * 
     * @param integer $id
     * @return CarteItem
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('IORestaurantBundle:CarteItem')->find($id);
        if (!$entity || $entity->getRestaurant()->getId() !== $this->userSv->getUserRestaurant()->getId()) {
            throw $this->createNotFoundException('Unable to find CarteItem entity.');
        }
        
        return $entity;
    }

}
