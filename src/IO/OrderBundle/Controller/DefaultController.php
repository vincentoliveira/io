<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/order")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="order_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
