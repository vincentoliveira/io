<?php

namespace IO\MenuBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use IO\MenuBundle\Entity\Dish;


/**
 * Dish Service
 */
class DishService
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
     * Get Restaurant dishes
     * 
     * @param String $restaurant
     * @return array
     */
    public function getRestaurantDishes($restaurantName)
    {
        $qb = $this->em->getRepository('IOMenuBundle:Dish')
                ->createQueryBuilder('dish')
                ->select('dish')
                ->leftJoin('dish.restaurant', 'restaurant')
                ->andWhere('restaurant.name = :restaurantName')
                ->setParameter(':restaurantName', $restaurantName)
                ->orderBy('dish.order', 'ASC');

        $dihes = $qb->getQuery()->getResult();
        
        $results = array();
        foreach ($dihes as $dish) {
            $results[] = $this->getJsonArray($dish);
        }

        return $results;
    }

       
    /**
     * Get json array
     * 
     * @param \IO\MenuBundle\Entity\Dish $dish
     * @return array
     */
    public function getJsonArray(Dish $dish = null)
    {
        if ($dish === null) {
            return null;
        }
        
        if ($this->mediaSv === null) {
            $this->mediaSv = $this->container->get('menu.media');
        }
        
        return array(
            'id' => $dish->getId(),
            'name' => $dish->getName(),
            'description' => $dish->getDescription(),
            'category' => $dish->getCategory() !== null ? $dish->getCategory()->getId() : 0,
            'price' => $dish->getPrice() ? $dish->getPrice() : 0,
            'media' => $this->mediaSv->getJsonArray($dish->getMedia()),
        );
    }
        
}
