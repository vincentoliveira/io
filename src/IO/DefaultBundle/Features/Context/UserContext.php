<?php

namespace IO\DefaultBundle\Features\Context;

use Behat\Behat\Context\Step;
use IO\UserBundle\Entity\User;
use IO\RestaurantBundle\Entity\Restaurant;

/**
 * User context.
 */
class UserContext extends AbstractContext
{
    /**
     * @Given /^l\'utilisateur "([^"]*)" existe et a le role "([^"]*)" du restaurant "([^"]*)"$/
     */
    public function lUtilisateurExisteEtALeRoleDuRestaurant($username, $role, $restaurantName)
    {
        $email = $username . '@innovorder.fr';
        $em = $this->getEntityManager();
        
        $user = $em->getRepository('IOUserBundle:User')->findOneBy(array('username' => $username));
        if ($user === null) {
            $user = new User();
            $user->setUsername($username);
        }
          
        $restaurant = $em->getRepository('IORestaurantBundle:Restaurant')->findOneBy(array('name' => $restaurantName));
        if ($restaurant === null) {
            $restaurant = new Restaurant();
            $restaurant->setName($restaurantName);
            $em->persist($restaurant);
        }
        
        $user->setEmail($email);
        $user->setPlainPassword($username);
        $user->setRoles(array($role));
        $user->setRestaurant($restaurant);
        $user->setEnabled(true);
        
        $em->persist($user);
        $em->flush();
    }

    /**
     * @Given /^je suis connecté en tant que "([^"]*)"$/
     */
    public function jeSuisConnecteEnTantQue($username)
    {
        $steps[] = new Step\Given('je suis sur "/login"');
        $steps[] = new Step\Given('je remplis "_username" avec "'.$username.'"');
        $steps[] = new Step\Given('je remplis "_password" avec "'.$username.'"');
        $steps[] = new Step\Given('je presse "_submit"');
        return $steps;
    }
    
    /**
     * @Given /^l\'utilisateur "([^"]*)" a pour salt "([^"]*)"$/
     */
    public function lUtilisateurAPourSalt($username, $salt)
    {
        $em = $this->getEntityManager();
        
        $user = $em->getRepository('IOUserBundle:User')->findOneBy(array('username' => $username));
        assertNotNull($user, sprintf('L\'utilisateur %s n\'existe pas', $username));
        
        $user->setSalt($salt);
        $em->persist($user);
        $em->flush();
    }

    /**
     * @Given /^l\'utilisateur "([^"]*)" n\'existe pas$/
     */
    public function lUtilisateurNExistePas($username)
    {
        $em = $this->getEntityManager();
        
        $user = $em->getRepository('IOUserBundle:User')->findOneBy(array('username' => $username));
        if ($user !== null) {
            $em->remove($user);
            $em->flush();
        }
    }

     /**
     * @Given /^l\'utilisateur "([^"]*)" a pour restaurant "([^"]*)"$/
     */
    public function lUtitlisateurAPourRestaurant($username, $restaurantName)
    {
        $em = $this->getEntityManager();
        
        $user = $em->getRepository('IOUserBundle:User')->findOneBy(array('username' => $username));
        assertNotNull($user, sprintf('L\'utilisateur %s n\'existe pas', $username));

        $restaurant = $em->getRepository('IORestaurantBundle:Restaurant')->findOneBy(array('name' => $restaurantName));
        assertNotNull($restaurant, sprintf('Le restaurant %s n\'existe pas', $restaurantName));
        
        $user->setRestaurant($restaurant);
        $em->persist($user);
        $em->flush();
    }
    
}
