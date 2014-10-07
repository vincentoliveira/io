<?php

namespace IO\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;
use IO\RestaurantBundle\Entity\Restaurant;

/**
 * AuthToken Repository
 */
class AuthTokenRepository extends EntityRepository
{

    /**
     * Is unique token
     * 
     * @param strng $token
     * @return boolean
     */
    public function isUniqueToken($token)
    {
        $queryBuilder = $this->createQueryBuilder('userToken');

        $queryBuilder->select('userToken')
                ->where('userToken.token = :token')
                ->andWhere('userToken.expiresAt > :now')
                ->setParameter(':token', $token)
                ->setParameter(':now', new \DateTime());

        $result = $queryBuilder->getQuery()->getArrayResult();
        return empty($result);
    }

    /**
     * Find token for restaurants
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return type
     */
    public function findTokensForRestaurant(Restaurant $restaurant)
    {
        $restaurantRepo = $this->getEntityManager()->getRepository('IORestaurantBundle:Restaurant');
        $managers = $restaurantRepo->findManagers($restaurant);
                
        $queryBuilder = $this->createQueryBuilder('userToken');
        $queryBuilder->select('userToken')
                ->where('userToken.user IN (:managers)')
                ->andWhere('userToken.expiresAt IS NULL')
                ->setParameter(':managers', $managers);

        $result = $queryBuilder->getQuery()->getResult();
        
        return $result;
    }
    
}