<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\DiExtraBundle\Annotation\Inject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Carte Controller.
 *
 * @Route("/carte/restaurant")
 */
class CarteController extends Controller
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
     * @Route("/{name}", name="carte_index")
     * @Secure("ROLE_MANAGER")
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
