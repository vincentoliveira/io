<?php

namespace IO\MenuBundle\Service;

use Doctrine\ORM\EntityManager;
use IO\MenuBundle\Entity\Restaurant;

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
                ->setParameter(':restaurantName', $restaurantName);
        
        $categories = $qb->getQuery()->getResult();
        
        $results = array();
        foreach ($categories as $category) {
            $results[] = array(
                'id' => $category->getId(),
                'name' => $category->getName(),
            );
        }
        
        return $results;
    }

}
