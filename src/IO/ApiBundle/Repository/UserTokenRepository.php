<?php

namespace IO\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserToken Repository
 */
class UserTokenRepository extends EntityRepository
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

}