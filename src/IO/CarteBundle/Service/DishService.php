<?php

namespace IO\CarteBundle\Service;

use Doctrine\ORM\EntityManager;

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
     * Constructor
     * 
     * @param EntityManager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get Restaurant dishes
     * 
     * @param String $restaurant
     * @return array
     */
    public function getRestaurantDishes($restaurantName)
    {
        $qb = $this->em->getRepository('IOCarteBundle:Dish')
                ->createQueryBuilder('dish')
                ->select('dish')
                ->leftJoin('dish.restaurant', 'restaurant')
                ->andWhere('restaurant.name = :restaurantName')
                ->setParameter(':restaurantName', $restaurantName)
                ->orderBy('dish.order', 'ASC');

        $dihes = $qb->getQuery()->getResult();

        $results = array();
        foreach ($dihes as $dish) {
            $results[] = array(
                'id' => $dish->getId(),
                'name' => $dish->getName(),
                'description' => $dish->getDescription(),
                'category' => $dish->getCategory()->getId(),
                'price' => $dish->getPrice() ? $dish->getPrice() : 0,
                'media' => $dish->getMedia(),
            );
        }

        return $results;
    }

}
