<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use IO\RestaurantBundle\Entity\CarteItem;

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
    protected function getEntity($id, $className = 'IORestaurantBundle:CarteItem')
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($className)->find($id);
        if (!$entity || (method_exists($entity, 'getRestaurant') && $entity->getRestaurant()->getId() !== $this->userSv->getUserRestaurant()->getId())) {
            throw $this->createNotFoundException('Unable to find ' . $className . ' entity.');
        }
        
        return $entity;
    }

}
