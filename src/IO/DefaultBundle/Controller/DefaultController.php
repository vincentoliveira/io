<?php

namespace IO\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Default controller
 * 
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;
    
    /**
     * @Route("/", name="homepage")
     * @Secure(roles="ROLE_USER")
     */
    public function indexAction()
    {
        $user = $this->userSv->getUser();
        if ($user->hasRole("ROLE_ADMIN")) {
            if ($this->userSv->getCurrentRestaurant()) {
                return $this->forward("IORestaurantBundle:Carte:edit");
            }
            return $this->forward("IORestaurantBundle:Admin:index");
        } elseif ($user->hasRole("ROLE_CHIEF")) {
            if ($this->userSv->getCurrentRestaurant()) {
                return $this->forward("IOOrderBundle:Dashboard:index");
            }
            return $this->forward("IORestaurantBundle:Restaurant:list");
        } elseif ($user->hasRole("ROLE_MANAGER")) {
            return $this->forward("IOOrderBundle:Dashboard:index");
        } else {
            return $this->forward("IOOrderBundle:Default:index");
        }
    }
}
