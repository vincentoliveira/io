<?php

namespace IO\CarteBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use IO\MenuBundle\Entity\Category;


/**
 * Category Service
 */
class CategoryService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Container
     */
    protected $container;
    
    /**
     * @var MediaService
     */
    protected $mediaSv;

    /**
     * Constructor
     * 
     * @param EntityManager
     */
    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Get Restaurant Categories
     * 
     * @param String $restaurant
     * @return array
     */
    public function getRestaurantCategories($restaurantName)
    {
        $qb = $this->em->getRepository('IOCarteBundle:Category')
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
            $results[] = $this->getJsonArray($category, 1);
            
            // add children (only 2 levels ?)
            foreach ($category->getChildren() as $children) {
                $results[] = $this->getJsonArray($children, 2);
            }
        }
        
        return $results;
    }

       
    /**
     * Get json array
     * 
     * 
     * @param \IO\MenuBundle\Service\Category $category
     * @param int $level
     * @return array
     */
    public function getJsonArray(Category $category = null, $level = 1)
    {
        if ($category === null) {
            return null;
        }
        
        if ($this->mediaSv === null) {
            $this->mediaSv = $this->container->get('menu.media');
        }
        
        return array(
            'id' => $category->getId(),
            'level' => $level,
            'name' => $category->getName(),
            'parent_id' => $category->getParent() !== null ? $category->getParent()->getId() : 0,
            'media' => $this->mediaSv->getJsonArray($category->getMedia()),
        );
    }

}
