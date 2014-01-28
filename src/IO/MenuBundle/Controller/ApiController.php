<?php

namespace IO\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * API Controller
 */
class ApiController extends Controller
{
    /**
     * Get all categories
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCategoriesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('IOMenuBundle:Category')
                ->createQueryBuilder('category')
                ->select('category, parent')
                ->leftJoin('category.parent', 'parent')
                ->getQuery()
                ->getArrayResult();
        
        return new JsonResponse($categories);
    }
    
    /**
     * Get all data
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getDishesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('IOMenuBundle:Dish')
                ->createQueryBuilder('dish')
                ->select('dish, category')
                ->leftJoin('dish.category', 'category')
                ->getQuery()
                ->getArrayResult();
        
        return new JsonResponse($categories);
    }
}
