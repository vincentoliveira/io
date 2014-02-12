<?php

namespace IO\MenuBundle\Controller;

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
        return $this->redirect($this->generateUrl('commande_en_cours'));
    }
}
