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
 * @Route("/carte")
 */
class CarteController extends Controller
{

    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;
    
    /**
     * CarteItem Service
     * 
     * @Inject("io.carte_item_service")
     * @var \IO\RestaurantBundle\Service\CarteItemService
     */
    public $carteItemSv;
    
    /**
     * Displays all categories
     *
     * @Route("/", name="carte_edit")
     * @Secure("ROLE_MANAGER")
     * @Template()
     */
    public function editAction()
    {
        $restaurant = $this->userSv->getCurrentRestaurant();
        
        $criteria = array(
            'restaurant' => $restaurant,
            'parent' => null,
            'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY,
        );
        $orderBy = array('position' => 'ASC');
        $em = $this->getDoctrine()->getManager();
        $carte = $em->getRepository('IORestaurantBundle:CarteItem')->findBy($criteria, $orderBy);
        
        return array(
            'restaurant' => $restaurant,
            'carte' => $carte,
        );
    }
    
    /**
     * Displays carte
     *
     * @Route("/restaurant/{name}", name="carte_index")
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
