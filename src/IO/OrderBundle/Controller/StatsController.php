<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\OrderBundle\Form\StatFilterType;

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
     * @Route("/evolution/turnover", name="stats_turnover_evolution")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function turnoverEvolutionAction(Request $request)
    {
        $now = new \DateTime();
        $aMonthAgo = new \DateTime();
        $aMonthAgo->sub(new \DateInterval('P1M'));
        $dates = array(
            'start_date' => $aMonthAgo,
            'end_date' => $now,
        );
        $filterForm = $this->createForm(new StatFilterType(), $dates);
        if ($request->query->has('start_date') || $request->query->has('end_date')) {
            $filterForm->submit($request);
            $dates = $filterForm->getData();
        }
        
        return array(
            'filters' => $filterForm->createView(),
        );
    }
    
    /**
     * @Route("/distribution/item", name="stats_item_distribution")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function itemDistributionAction(Request $request)
    {
        $restaurant = $this->userSv->getCurrentRestaurant();
        
        $filters = array();
        $filtersForm = $this->createForm(new StatFilterType(), $filters);
        
        if ($request->isMethod('POST')) {
            $filtersForm->submit($request);
            $filters = $filtersForm->getData();
        }
        $filters['restaurant_id'] = $restaurant->getId();
        
        $distributions = array();
        $distributions['Globale'] = array(
            'global_pie' => $this->distribSv->getGlobalDistribution($filters, "pie", 'global_pie'),
            'global_bar' => $this->distribSv->getGlobalDistribution($filters, "bar", 'global_bar'),
        );
        
        $repositorty = $this->getDoctrine()->getRepository('IORestaurantBundle:CarteItem');
        $categories = $repositorty->getRestaurantMainCategory($restaurant->getId());
        foreach ($categories as $category) {
            $name = preg_replace("/[^A-Za-z0-9]/", '_', $category->getShortName());
            $pieId = $name . '_pie';
            $barId = $name . '_bar';
            
            $filters['parent_id'] = $category->getId();
            $distributions[$name] = array(
                $pieId => $this->distribSv->getCategoryDistribution($filters, "pie", $pieId, $category->getName()),
                $barId => $this->distribSv->getCategoryDistribution($filters, "bar", $barId, $category->getName()),
            );
        }
        
        return array(
            'filters' => $filtersForm->createView(),
            'distributions' => $distributions,
        );
    }
    
    
    /**
     * @Route("/distribution/time", name="stats_time_distribution")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function timeDistributionAction()
    {
        $restaurant = $this->userSv->getCurrentRestaurant();
        
        $chartId = 'time_distribution';
        $chart =$this->distribSv->getTimeDistribution($restaurant, $chartId);

        return array(
            'chart' => $chart,
            'chartId' => $chartId,
        );
    }
    
    
    /**
     * @Route("/distribution/payment", name="stats_payment_distribution")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function paymentDistributionAction()
    {
        $restaurant = $this->userSv->getCurrentRestaurant();
        
        $chartId = 'payment_distribution';
        $chart =$this->distribSv->getPaymentDistribution($restaurant, $chartId);
        
        
        $chartId2 = 'payment_amount_distribution';
        $chart2 =$this->distribSv->getPaymentAmountDistribution($restaurant, $chartId2);
        
        return array(
            'chart' => $chart,
            'chartId' => $chartId,
            'chart2' => $chart2,
            'chartId2' => $chartId2,
        );
    }
}
