<?php

namespace IO\CarteBundle\Service;

use Doctrine\ORM\EntityManager;

/**
 * DishOption Service
 */
class DishOptionService
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
     * Get paginated MenuOption list
     * 
     * @return array
     */
    public function getList($restaurantId)
    {
        $repo = $this->em->getRepository('IOCarteBundle:DishOption');
        $queryBuilder = $repo->createQueryBuilder('option')
                ->where('option.restaurant = :restaurant')
                ->setParameter(':restaurant', $restaurantId);


        $list = $queryBuilder->getQuery()->getResult();
        
        return $list;
    }


}
