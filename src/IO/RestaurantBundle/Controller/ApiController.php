<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Description of ApiController
 * 
 * @Route("/api/restaurant")
 */
class ApiController extends Controller
{
    /**
     * Admin restaurant index
     * 
     * @return type
     * @Route("/carte", name="restaurant_api_get_carte")
     * @Secure(roles="ROLE_TABLETTE")
     */
    public function getCarteAction()
    {
        $carte = array();
        return new JsonResponse(array('carte' => $carte));
    }
}
