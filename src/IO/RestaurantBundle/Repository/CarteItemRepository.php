<?php

namespace IO\RestaurantBundle\Repository;

use Doctrine\ORM\EntityRepository;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

/**
 * CarteItem Repository
 */
class CarteItemRepository extends EntityRepository
{

    /**
     * 
     * @param int $restaurantId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getRestaurantCategoryQueryBuilder($restaurantId)
    {
        $queryBuilder = $this->createQueryBuilder('item');

        $queryBuilder->select('item')
                ->where('item.restaurant = :restaurant')
                ->andWhere('item.itemType = :type')
                ->setParameter(':restaurant', $restaurantId)
                ->setParameter(':type', ItemTypeEnum::TYPE_CATEGORY)
                ->addOrderBy('item.parent', 'ASC')
                ->addOrderBy('item.position', 'ASC');

        return $queryBuilder;
    }
    /**
     * 
     * @param int $restaurantId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getRestaurantOptionListQueryBuilder($restaurantId)
    {
        $queryBuilder = $this->createQueryBuilder('item');

        $queryBuilder->select('item')
                ->where('item.restaurant = :restaurant')
                ->andWhere('item.itemType = :type')
                ->setParameter(':restaurant', $restaurantId)
                ->setParameter(':type', ItemTypeEnum::TYPE_OPTION)
                ->addOrderBy('item.parent', 'ASC')
                ->addOrderBy('item.position', 'ASC');

        return $queryBuilder;
    }
    

    /**
     * 
     * @param int $restaurantId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getRestaurantMainCategory($restaurantId)
    {
        $queryBuilder = $this->createQueryBuilder('item');

        $queryBuilder->select('item')
                ->where('item.restaurant = :restaurant')
                ->andWhere('item.itemType = :type')
                ->andWhere('item.parent IS NULL')
                ->setParameter(':restaurant', $restaurantId)
                ->setParameter(':type', ItemTypeEnum::TYPE_CATEGORY)
                ->addOrderBy('item.position', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

}