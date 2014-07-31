<?php

namespace IO\UserBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\DependencyInjection\Container;
use IO\UserBundle\Entity\User;
use IO\RestaurantBundle\Entity\Restaurant;
/**
 * User Service
 * 
 * @Service("io.user_service")
 */
class UserService
{

    /**
     * Container
     * 
     * @Inject("service_container")
     * @var Container
     */
    public $container;
    
    /**
     * Container
     * 
     * @Inject("session")
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    public $session;

    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * Fos User Manager
     * 
     * @Inject("fos_user.user_manager")
     * @var \FOS\UserBundle\Model\UserManager
     */
    public $userManager;

    /**
     * User token service
     * 
     * @Inject("io.user_token_service")
     * @var \IO\ApiBundle\Service\UserTokenService
     */
    public $userTokenSv;
    
    /**
     * Get loggued user
     *
     * @return \IO\UserBundle\Entity\User|null
     */
    public function getUser()
    {
        $token = $this->container->get('security.context')->getToken();
        $user = $token !== null ? $token->getUser() : null;

        return $user instanceof User ? $user : null;
    }
    
    /**
     * Get loggued user restaurant
     *
     * @return \IO\RestaurantBundle\Entity\User|null
     */
    public function getCurrentRestaurant()
    {
        $user = $this->getUser();
        if ($user instanceof User && !$user->hasRole("ROLE_CHIEF") && !$user->hasRole("ROLE_ADMIN")) {
            return $user->getRestaurant();
        }
        
        $session = $this->session;
        $id = $session->get("user.restaurant");
        if ($id === null) {
            return null;
        }
        
        return $this->em->getRepository('IORestaurantBundle:Restaurant')->find($id);
    }
    
    /**
     * Authentification user
     *
     * @return \IO\ApiBundle\Entity\UserToken
     */
    public function authUser(array $data)
    {
        if (!isset($data['email']) || !isset($data['plainPassword'])) {
            return null;
        }
        
        $repo = $this->em->getRepository('IOUserBundle:User');
        $user = $repo->findOneByEmail($data['email']);
        if ($user === null || !$user->isEnabled()) {
            return null;
        }
        
        $hashPwd = $user->getPassword();
        $user->setPlainPassword($data['plainPassword']);
        $this->userManager->updatePassword($user);
        if ($hashPwd !== $user->getPassword()) {
            return null;
        }
        
        return $this->userTokenSv->createToken($user);
    }
    
    
    /**
     * Create a user from array
     * 
     * @param array $data
     * @return \IO\RestaurantBundle\Entity\User|null
     */
    public function createUser(array $data)
    {
        $user = new User();
        if (!isset($data['username']) || !isset($data['email']) || !isset($data['plainPassword'])) {
            return null;
        }
        
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPlainPassword($data['plainPassword']);
        $user->setEnabled(true);
        
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }
        
        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (\Exception $ex) {
            return null;
        }
        
        return $user;
    }
    
    
    /**
     * Set current user restaurant
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     */
    public function setCurrentRestaurant(Restaurant $restaurant)
    {
        $session = $this->session;
        $session->set("user.restaurant", $restaurant->getId());
        $session->save();
    }

}
