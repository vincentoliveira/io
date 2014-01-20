<?php

namespace IO\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Commande Controller
 */
class CommandeController extends Controller
{
    /**
     * Display home
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @Secure(roles="ROLE_CUISINIER")
     */
    public function commandeEnCoursAction()
    {        
        return array();
    }
}
