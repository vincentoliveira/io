<?php

namespace IO\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class RestaurantController extends Controller
{
    /**
     * Admin restaurant index
     * 
     * @return type
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        $restaurants = $this->getDoctrine()->getRepository("IOCarteBundle:Restaurant")->findAll();
        return array('restaurants' => $restaurants);
    }
}
