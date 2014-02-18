<?php

namespace IO\MenuBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * Category Service
 */
class CategoryService
{
    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     * 
     * @param EntityManager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * Get Restaurant Categories
     * 
     * @param String $restaurant
     * @return array
     */
    public function getRestaurantCategories($restaurantName)
    {
        $qb = $this->em->getRepository('IOMenuBundle:Category')
                ->createQueryBuilder('category')
                ->select('category')
                ->leftJoin('category.restaurant', 'restaurant')
                ->andWhere('category.parent IS NULL')
                ->andWhere('restaurant.name = :restaurantName')
                ->setParameter(':restaurantName', $restaurantName)
                ->orderBy('category.order', 'ASC');
        
        $categories = $qb->getQuery()->getResult();
        
        $results = array();
        foreach ($categories as $category) {
            $results[] = array(
                'id' => $category->getId(),
                'name' => $category->getName(),
                'parent' => 0,
                'media' => $category->getMedia(),
            );
            
            // add children (only 2 levels ?)
            foreach ($category->getChildren() as $children) {
                $results[] = array(
                    'id' => $children->getId(),
                    'name' => $children->getName(),
                    'parent' => $category->getId(),
                    'media' => $children->getMedia(),
                );
            }
        }
        
        return $results;
    }

}
