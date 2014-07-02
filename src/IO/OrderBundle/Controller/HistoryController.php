<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\OrderBundle\Entity\OrderData;
use IO\OrderBundle\Enum\OrderStatusEnum;

/**
 * @Route("/order/history")
 */
class HistoryController extends Controller
{
    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;
    
    /**
     * HistoryService Service
     * 
     * @Inject("io.history_service")
     * @var \IO\OrderBundle\Service\HistoryService
     */
    public $historySv;
    
    /**
     * PaymentHistory Service
     * 
     * @Inject("io.payment_history_service")
     * @var \IO\OrderBundle\Service\PaymentHistoryService
     */
    public $paymentHistorySv;
    
    /**
     * @Route("/", name="history_index")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function indexAction()
    {
        $restaurant = $this->userSv->getUserRestaurant();
        $history = $this->historySv->getOrderHistoryPerDay($restaurant);
        
        return array(
            'history' => $history,
        );
    }
    
    
    /**
     * @Route("/day/{dateStr}", name="history_day")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function dayAction($dateStr) 
    {
        $date = \DateTime::createFromFormat("d-m-Y", $dateStr);
        $restaurant = $this->userSv->getUserRestaurant();
        $history = $this->historySv->getDayHistory($date, $restaurant);
        
        return array(
            'day' => $date,
            'history' => $history,
        );
    }
    
    
    /**
     * @Route("/payments", name="history_payment")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function paymentAction(Request $request) 
    {
        $restaurant = $this->userSv->getUserRestaurant();
        $payments = $this->paymentHistorySv->getPayments($restaurant, $request->query->all());
        
        return array(
            'payments' => $payments,
        );
    }
    
}
