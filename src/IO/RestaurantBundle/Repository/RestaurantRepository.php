<?php

namespace IO\RestaurantBundle\Repository;

use Doctrine\ORM\EntityRepository;
use IO\RestaurantBundle\Entity\Restaurant;

/**
 * Restaurant Repository
 */
class RestaurantRepository extends EntityRepository
{

    /**
     * Find the first manager of a restaurant
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\UserBundle\Entity\User|null Description
     */
    public function findFirstManager(Restaurant $restaurant)
    {
        $userRepo = $this->getEntityManager()->getRepository('IOUserBundle:User');
        $users = $userRepo->findBy(array('restaurant' => $restaurant));
        if (!empty($users)) {
            foreach ($users as $user) {
                if ($user->hasRole('ROLE_MANAGER')) {
                    return $user;
                }
            }
        }
        
        $chiefs = $userRepo->findBy(array('restaurantGroup' => $restaurant->getGroup()));
        if (!empty($chiefs)) {
            foreach ($chiefs as $chief) {
                if ($chief->hasRole('ROLE_CHIEF')) {
                    return $chief;
                }
            }
        }
        
        return null;
    }

    /**
     * Find all managers of a restaurant
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return array Array of User
     */
    public function findManagers(Restaurant $restaurant)
    {
        $managers = array();
        
        $userRepo = $this->getEntityManager()->getRepository('IOUserBundle:User');
        $users = $userRepo->findBy(array('restaurant' => $restaurant));
        if (!empty($users)) {
            foreach ($users as $user) {
                if ($user->hasRole('ROLE_MANAGER')) {
                    $managers[] = $user;
                }
            }
        }
        
        $chiefs = $userRepo->findBy(array('restaurantGroup' => $restaurant->getGroup()));
        if (!empty($chiefs)) {
            foreach ($chiefs as $chief) {
                if ($chief->hasRole('ROLE_CHIEF')) {
                    $managers[] =  $chief;
                }
            }
        }
        
        return $managers;
    }

}