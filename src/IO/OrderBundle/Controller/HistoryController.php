<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\OrderBundle\Form\StatFilterType;

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
    public function indexAction(Request $request)
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
        
        $restaurant = $this->userSv->getCurrentRestaurant();
        $history = $this->historySv->getOrderHistoryPerDay($restaurant, $dates);
        
        if ($request->query->has('export-action')) {
            return $this->historyExport($history);
        }
        
        return array(
            'history' => $history,
            'filters' => $filterForm->createView(),
        );
    }
    
    protected function historyExport($history)
    {
        $handle = fopen('php://memory', 'r+');
        $dayName = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
        $headers = array('Date', 'Jour', 'Nombre de commandes', 'Total TTC', 'Total TVA', 'Total HT');
        fputcsv($handle, $headers, ';');
        
        $totals = array('Total', '', 0, 0, 0, 0);
        foreach ($history as $dayHistory) {
            $data = array(
                $dayHistory['date']->format('d-m-Y'),
                $dayName[intval($dayHistory['date']->format('w'))],
                $dayHistory['count'],
                $dayHistory['total'],
                $dayHistory['total_vat'],
                $dayHistory['total'] - $dayHistory['total_vat'],
            );
            fputcsv($handle, $data, ';');
            
            $totals[2] += $dayHistory['count'];
            $totals[3] += $dayHistory['total'];
            $totals[4] += $dayHistory['total_vat'];
            $totals[5] += $dayHistory['total'] - $dayHistory['total_vat'];
        }
        fputcsv($handle, $totals, ';');
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        $response = new Response();
        $response->setContent($content);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="io-export.csv"');

        return $response;
    }




    /**
     * @Route("/day/{dateStr}", name="history_day")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function dayAction($dateStr) 
    {
        $date = \DateTime::createFromFormat("d-m-Y", $dateStr);
        $restaurant = $this->userSv->getCurrentRestaurant();
        $history = $this->historySv->getDayHistory($date, $restaurant);
        
        return array(
            'day' => $date,
            'history' => $history,
        );
    }
    
    
    /**
     * @Route("_payments", name="history_payment")
     * @Template()
     * @Secure("ROLE_MANAGER")
     */
    public function paymentAction(Request $request) 
    {
        $restaurant = $this->userSv->getCurrentRestaurant();
        $payments = $this->paymentHistorySv->getPayments($restaurant, $request->query->all());
        
        return array(
            'payments' => $payments,
        );
    }
    
}
