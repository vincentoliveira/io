<?php

namespace IO\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\ApiBundle\Utils\ApiElementVisitor;

/**
 * User API Controller
 * 
 * @Route("/user")
 */
class UserController extends DefaultController
{

    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;

    /**
     * POST /user/create.json
     * 
     * Create a user from the json data post in the request
     * 
     * @param Request $request
     * @return JsonResponse
     * @Route("/create.json", name="api_user_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
        if ($data === null || empty($data)) {
            return $this->errorResponse(self::EMPTY_PARAMETER);
        }

        $user = $this->userSv->createUser($data);
        if ($user === null) {
            return $this->errorResponse(self::BAD_PARAMETER);
        }

        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('user' => $apiVisistor->visitUser($user)));
    }

    /**
     * POST /user/auth.json
     * 
     * Authentificate a user and return auth token
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/auth.json", name="api_user_auth")
     * @Method("POST")
     */
    public function authAction(Request $request)
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
        if ($data === null || empty($data)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }

        $userToken = $this->userSv->authUser($data);
        if ($userToken === null) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }

        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('auth' => $apiVisistor->visitUserToken($userToken)));
    }

}
