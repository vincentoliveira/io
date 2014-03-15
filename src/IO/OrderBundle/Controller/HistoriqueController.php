<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\OrderBundle\Form\HistoriqueFilterType;

/**
 * Historique Controller
 */
class HistoriqueController extends Controller
{
    const FILTERS_SESSION = "historique_filters_session";
    
    /**
     * Display historique
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function indexAction(Request $request)
    {

        $dateFrom = new \DateTime();
        $dateFrom->modify('first day of last month');
        $dateTo = new \DateTime();
        $dateTo->modify('last day of last month');
        $data = array(
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        );
        
        $form = $this->createForm(new HistoriqueFilterType(), $data);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            $data = $form->getData();
            $dateFrom = $data['dateFrom'] ? $data['dateFrom'] : $dateFrom;
            $dateTo = $data['dateTo'] ? $data['dateTo'] : $dateTo;
            $dateFrom->setTime(0, 0, 0);
            $dateTo->setTime(23, 59, 59);
            
            $session = $this->container->get('session');
            if (!$session->isStarted()) {
                $session->start();
            }
            $session->set(self::FILTERS_SESSION, $data);
        }

        $userSv = $this->container->get('user.user_service');
        $user = $userSv->getUser();
            
        $historiqueSv = $this->container->get('order.historique_service');
        $historique = $historiqueSv->getHistorique($user->getRestaurant(), $dateFrom, $dateTo);

        return array(
            'form' => $form->createView(),
            'historique' => $historique,
        );
    }

    /**
     * Export historique into a csv file
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Secure(roles="ROLE_RESTAURATEUR")
     */
    public function exportAction()
    {
        $session = $this->container->get('session');
        if (!$session->isStarted()) {
            $session->start();
        }
        
        $filters = $session->get(self::FILTERS_SESSION, array());
        if (isset($filters['dateFrom'])) {
            $dateFrom = $filters['dateFrom'];
        } else {
            $dateFrom = new \DateTime();
            $dateFrom->modify('first day of last month');
        }
        if (isset($filters['dateTo'])) {
            $dateTo = $filters['dateTo'];
        } else {
            $dateTo = new \DateTime();
            $dateTo->modify('last day of last month');
        }
        
        $dateFrom->setTime(0, 0, 0);
        $dateTo->setTime(23, 59, 59);
        
        $userSv = $this->container->get('user.user_service');
        $user = $userSv->getUser();
            
        $historiqueSv = $this->container->get('order.historique_service');
        $content = $historiqueSv->getCsv($user->getRestaurant(), $dateFrom, $dateTo);
        $filename = sprintf("historique_%s_%s.csv", $dateFrom->format('dmy'), $dateTo->format('dmy'));
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setContent($content);
        
        return $response;
    }

}