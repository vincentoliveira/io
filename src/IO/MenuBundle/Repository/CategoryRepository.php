<?php

namespace IO\MenuBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Category Repository
 */
class CategoryRepository extends EntityRepository
{

    /**
     * 
     * @param int $restaurantId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getRestaurantCategoryQueryBuilder($restaurantId)
    {
        $queryBuilder = $this->createQueryBuilder('category');

        $queryBuilder->select('category')
                ->where('category.restaurant = :restaurant')
                ->setParameter(':restaurant', $restaurantId)
                ->orderBy('category.order', 'ASC');

        return $queryBuilder;
    }

}