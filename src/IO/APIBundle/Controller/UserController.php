<?php

namespace IO\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * API User controller
 */
class UserController extends Controller
{
    /**
     * Get user salt
     * WSSE : <strong>OFF</strong>
     * Parameters :
     * - <strong>GET</strong> <em>username</em>
     * 
     * @return JsonResponse
     */
    public function saltAction(Request $request)
    {
        $username = $request->query->get('username');
        if ($username === null) {
            return new JsonResponse(array('status' => 'ko'));
        }
        
        $user = $this->getDoctrine()
                ->getRepository('IOUserBundle:User')
                ->findOneByUsername($username);
        
        if ($user === null) {
            return new JsonResponse(array('status' => 'ko'));
        }
        
        return new JsonResponse(array('status' => 'ok', 'salt' => $user->getSalt()));
    }
    
    
    /**
     * Check login
     * WSSE : <strong>ON</strong>
     * 
     * @return JsonResponse
     */
    public function checkLoginAction()
    {
        $token = $this->container->get('security.context')->getToken();
        if ($token !== null) {
            return new JsonResponse(array('status' => 'ok', 'login' => false, 'reason' => 'no wsse token'));
        }
        
        $user = $token->getUser();
        if ($user === null || !$user instanceof \IO\UserBundle\Entity\User) {
            return new JsonResponse(array('status' => 'ok', 'login' => false, 'reason' => 'user does not exist'));
        }
        
        return new JsonResponse(array('status' => 'ok', 'login' => true));
    }
}
