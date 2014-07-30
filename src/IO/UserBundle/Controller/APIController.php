<?php

namespace IO\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * API User controller
 * 
 * @Route("/api")
 */
class APIController extends Controller
{

    /**
     * Get user salt
     * WSSE : <strong>OFF</strong>
     * Parameters :
     * - <strong>GET</strong> <em>username</em>
     * 
     * @return JsonResponse
     * @Route("/salt.json", name="api_get_salt")
     */
    public function saltAction(Request $request)
    {
        $username = $request->query->get('username');
        if ($username === null) {
            return new JsonResponse(array('salt' => null));
        }

        $user = $this->getDoctrine()
                ->getRepository('IOUserBundle:User')
                ->findOneByUsername($username);
        if ($user === null) {
            return new JsonResponse(array('salt' => null));
        }

        return new JsonResponse(array('salt' => $user->getSalt()));
    }

    /**
     * Check login
     * WSSE : <strong>ON</strong>
     * 
     * @return JsonResponse
     * @Route("/check_login.json", name="api_check_login")
     */
    public function checkLoginAction()
    {
        $token = $this->container->get('security.context')->getToken();
        if ($token === null) {
            return new JsonResponse(array('login' => false, 'reason' => 'No token'));
        }

        $user = $token->getUser();
        if ($user === null || !$user instanceof \IO\UserBundle\Entity\User) {
            return new JsonResponse(array('login' => false, 'reason' => 'Bad token'));
        }

        return new JsonResponse(array('login' => true));
    }

}
