<?php

namespace IO\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * API User controller
 * 
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * Return salt of <em>username</em>, or null if <em>username</em> does not
     * exist
     * 
     * Result : 
     * <code>
     * {
     *  "authentication":"true",
     *  "id":1,
     *  "role":"ROLE_CLIENT",
     *  "client_token":"azeqsd"
     * }</code>
     * 
     * Parameters :
     * - <strong>POST</strong> <em>username</em>
     * - <strong>POST</strong> <em>password</em>
     * 
     * @return JsonResponse
     * @Route("/authentification.json", name="api_user_authorisation")
     * @Method("GET")
     */
    public function authenticationAction()
    {
        
    }

}
