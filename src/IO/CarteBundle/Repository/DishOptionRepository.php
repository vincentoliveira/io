<?php

namespace IO\CarteBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Category Repository
 */
class DishOptionRepository extends EntityRepository
{

    /**
     * 
     * @param int $restaurantId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findWithRestaurant($id, $restaurantId)
    {
        return $this->findOneBy(array(
            'id' => $id,
            'restaurant' => $restaurantId,
        ));
    }

}