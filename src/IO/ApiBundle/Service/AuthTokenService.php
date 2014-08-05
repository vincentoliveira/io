<?php

namespace IO\ApiBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\DependencyInjection\Container;
use IO\ApiBundle\Entity\AuthToken;
use IO\UserBundle\Entity\User;

/**
 * User Service
 * 
 * @Service("io.auth_token_service")
 */
class AuthTokenService
{

    static $validityTime = 86400; // 24 * 60 * 60

    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * Create token
     * 
     * @param \IO\UserBundle\Entity\User $user
     * @return \IO\ApiBundle\Entity\AuthToken
     */
    public function createToken(User $user)
    {
        $expirationDate = new \DateTime();
        $expirationDate->add(new \DateInterval('PT' . self::$validityTime . 'S'));

        $userToken = new AuthToken();
        $userToken->setToken($this->generateToken());
        $userToken->setUser($user);
        $userToken->setExpireAt($expirationDate);

        if ($user->getRestaurant()) {
            $userToken->addRestrictedRestaurant($user->getRestaurant());
        }
        if ($user->getRestaurantGroup()) {
            foreach ($user->getRestaurantGroup()->getRestaurants() as $restaurant) {
                $userToken->addRestrictedRestaurant($restaurant);
            }
        }

        $this->em->persist($userToken);
        $this->em->flush();

        return $userToken;
    }

    protected function generateToken()
    {
        $repo = $this->em->getRepository("IOApiBundle:AuthToken");
        $token = $this->getToken(16);
        for ($i = 0; $i < 15; $i++) {
            if ($repo->isUniqueToken($token)) {
                return $token;
            }
            $token .= $this->getToken(1);
        }
    }

    protected function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[mt_rand(0, strlen($codeAlphabet) - 1)];
        }
        return $token;
    }

}
