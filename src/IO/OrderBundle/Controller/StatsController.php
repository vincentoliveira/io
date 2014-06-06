<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * @Route("/order/stats")
 */
class StatsController extends Controller
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
     * @Inject("io.distribution_service")
     * @var \IO\OrderBundle\Service\DistributionService
     */
    public $distribSv;
    
    /**
     * @Route("/distribution", name="stats_distribution")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function distributionAction()
    {
        $restaurant = $this->userSv->getUserRestaurant();
        
        $distributions = array();
        $distributions['Globale'] = array(
            'global_pie' => $this->distribSv->getGlobalRepartition($restaurant, "pie", 'global_pie'),
            'global_bar' => $this->distribSv->getGlobalRepartition($restaurant, "bar", 'global_bar'),
        );
        
        $repositorty = $this->getDoctrine()->getRepository('IORestaurantBundle:CarteItem');
        $categories = $repositorty->getRestaurantMainCategory($restaurant->getId());
        foreach ($categories as $category) {
            $name = preg_replace("/[^A-Za-z0-9]/", '', $category->getShortName());
            $pieId = $name . '_pie';
            $barId = $name . '_bar';
            $distributions[$name] = array(
                $pieId => $this->distribSv->getCategoryRepartition($restaurant, $category, "pie", $pieId),
                $barId => $this->distribSv->getCategoryRepartition($restaurant, $category, "bar", $barId),
            );
        }
        
        return array(
            'distributions' => $distributions,
        );
    }
    
}
