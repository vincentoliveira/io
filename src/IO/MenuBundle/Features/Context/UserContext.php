<?php

namespace IO\MenuBundle\Features\Context;

use Behat\Behat\Context\Step;
use IO\UserBundle\Entity\User;

/**
 * User context.
 */
class UserContext extends AbstractContext
{

    /**
     * @Given /^l\'utilisateur "([^"]*)" existe et a le role "([^"]*)"$/
     */
    public function lUtilisateurExisteEtALeRole($username, $role)
    {
        $email = $username . '@innovorder.fr';
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        
        $user = $em->getRepository('IOUserBundle:User')->findOneBy(array('username' => $username));
        if ($user === null) {
            $user = new User();
            $user->setUsername($username);
        }
        
        $user->setEmail($email);
        $user->setPlainPassword($username);
        $user->setRoles(array($role));
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
}
