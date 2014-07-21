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
        return $this->em->getRepository('IORestaurantBundle:Restaurant')->find($id);
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
