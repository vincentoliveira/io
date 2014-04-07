<?php

namespace IO\CarteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Default Controller
 */
class DefaultController extends Controller
{

    /**
     * Home page (redicrection to "Commande en cours")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse redirection
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('admin_homepage'));
        } else {
            return $this->redirect($this->generateUrl('commande_en_cours'));
        }
    }

}
