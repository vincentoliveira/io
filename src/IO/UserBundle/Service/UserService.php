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
     * @return \IO\UserBundle\Entity\User|null
     */
    public function authUser(array $data)
    {
        $token = $this->container->get('security.context')->getToken();
        $user = $token !== null ? $token->getUser() : null;

        return $user instanceof User ? $user : null;
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
