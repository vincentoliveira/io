<?php

namespace IO\UserBundle\Service;

use Symfony\Component\DependencyInjection\Container;
use IO\UserBundle\Entity\User;

/**
 * User Service
 */
class UserService
{
    /**
     *
     * @var Container
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
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

}
