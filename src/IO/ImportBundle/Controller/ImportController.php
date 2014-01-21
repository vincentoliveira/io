<?php

namespace IO\ImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use IO\MenuBundle\Form\SelectRestaurantType;

/**
 * 
 */
class ImportController extends Controller
{
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        $form = $this->createForm(new SelectRestaurantType());
        return array(
            'form' => $form->createView(),
        );
    }
    
    
    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template("IOImportBundle:Import:index.html.twig")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function importAction(Request $request)
    {
        $form = $this->createForm(new SelectRestaurantType());
        $form->bind($request);
        
        if ($form->isValid()) {
            $data = $form->getData();
            $restaurant = $data['restaurant'];
            
            $importSv = $this->container->get('io_import.import_service');
            $results = $importSv->import($restaurant);
        } else {
            $results = array();
            $results['success'] = false;
            $results['message'] = 'Ce restaurant n\'existe pas';
        }
        
        $session = $this->container->get('session');
        if ($results['success'] === false) {
            $session->getFlashBag()->add('error', $results['message']);
        } else {
            $session->getFlashBag()->add('success', 'L\'import s\'est exécuté avec succès');
        }
        
        return array(
            'form' => $form->createView(),
            'results' => $results,
        );
    }
}
