<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * @Route("/order/remote")
 */
class RemoteController extends Controller
{
    /**
     * CarteItem Service
     * 
     * @Inject("io.carte_item_service")
     * @var \IO\RestaurantBundle\Service\CarteItemService
     */
    public $carteItemSv;
    
    /**
     * Displays carte
     *
     * @Route("/{name}", name="remote_order_index")
     * @Template()
     */
    public function indexAction($name)
    {
        $repo = $this->getDoctrine()->getRepository('IORestaurantBundle:Restaurant');
        $restaurant = $repo->findOneByName($name);
        
        if ($restaurant === null) {
            throw $this->createNotFoundException('Unable to find Restaurant entity.');
        }
        
        $carte = $this->carteItemSv->getCarte($restaurant);
        return array(
            'restaurant' => $restaurant,
            'carte' => $carte,
        );
    }
}
