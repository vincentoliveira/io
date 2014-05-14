<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/order")
 */
class ApiController extends Controller
{
    /**
     * @Route("/", name="order_api_order")
     */
    public function orderAction(Request $request)
    {
        $response = array('status' => false, 'message' => 'Empty command');
        return new JsonResponse($response);
    }
}
