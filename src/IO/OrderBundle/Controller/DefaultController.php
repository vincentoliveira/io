<?php

namespace IO\OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller
{    
    /**
     * Order en cours
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
