<?php

namespace IO\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use IO\UserBundle\Form\UserType;
use IO\UserBundle\Entity\User;

class UserController extends Controller
{
    /**
     * Admin user index
     * 
     * @return type
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository("IOUserBundle:User")->findAll();
        return array('users' => $users);
    }
    
    /**
     * Admin add user
     * 
     * @return type
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);
        
        if ($request->isMethod("POST")) {
            $form->bind($request);
            
            $user->setEnabled(true);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            
            $session = $this->container->get('session');
            $session->getFlashBag()->add('success', 'L\'utilisateur à bien été crée.');
            
            return $this->redirect($this->generateUrl('admin_users'));
        }
        
        return array('form' => $form->createView());
    }
}
