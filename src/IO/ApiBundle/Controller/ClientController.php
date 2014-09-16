<?php

namespace IO\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\ApiBundle\Utils\ApiElementVisitor;
use IO\ApiBundle\Utils\BadParameterException;
use IO\UserBundle\Entity\User;

/**
 * User API Controller
 * 
 * @Route("/client")
 */
class ClientController extends DefaultController
{

    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;

    /**
     * User token service
     * 
     * @Inject("io.auth_token_service")
     * @var \IO\ApiBundle\Service\AuthTokenService
     */
    public $userTokenSv;

    /**
     * POST /client/create.json
     * 
     * Create a user from the json data post in the request.
     * Return auth token for the new user.
     * 
     * Parameters:
     * - <strong>email</strong> Email of the user (string)
     * - <strong>plainPassword</strong> Plain password of the user (string)
     * - <strong>firstname</strong> Firstname of the user (string)
     * - <strong>lastname</strong> Lastname of the user (string)
     * - <strong>birthdate</strong> Birthdate of the user [Y-m-d] (string)
     * 
     * @Route("/create.json", name="api_client_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $data = $request->request->all();
        if ($data === null || empty($data)) {
            return $this->errorResponse(self::EMPTY_PARAMETER);
        }

        try {
            $phone2 = $address = null;
            if (isset($data['phone'][1])) {
                $phone2 = $this->userSv->createPhoneNumber($data['phone'][1]);
            }
            if (isset($data['address'])) {
                $address = $this->userSv->createAddress($data['address']);
            }
        } catch (\Exception $e) {
            // skip error
        }

        try {
            $em = $this->getDoctrine()->getManager();
            
            $identity = $this->userSv->createUserIdentity($data);
            $em->persist($identity);
            
            if (isset($data['phone'][0])) {
                $phone1 = $this->userSv->createPhoneNumber($data['phone'][0]);
                $identity->setPhone1($phone1);
                $em->persist($phone1);
            }
            
            if ($phone2 !== null) {
                $identity->setPhone2($phone2);
                $em->persist($phone2);
            }
            
            if ($address !== null) {
                $identity->setAddress1($address);
                $em->persist($address);
            }
            
            $user = $this->userSv->createUser($data);
            $user->setIdentity($identity);
            $em->persist($user);
            $em->flush();
        } catch (BadParameterException $e) {
            return $this->errorResponse(self::BAD_PARAMETER, $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse(self::INTERNAL_ERROR, $e->getMessage());
        }
        
        $userToken = $this->userTokenSv->createToken($user);

        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('client_token' => $userToken->accept($apiVisistor)));
    }

    
    /**
     * POST /client/auth.json
     * 
     * Authenticate a client from its login/paasword.
     * Return auth token
     * 
     * Parameters:
     * - <strong>email</strong> Email of the user you want to authenticate 
     *                          (string)
     * - <stroong>plainPassword</strong> Plain password of the user you want to 
     *                                   authenticate (string)
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/auth.json", name="api_client_auth")
     * @Method("POST")
     */
    public function authAction(Request $request)
    {
        $data = $request->request->all();
        if ($data === null || empty($data)) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }

        $user = $this->userSv->authUserData($data);
        if ($user === null || $user->getIdentity() === null) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $userToken = $this->userTokenSv->createToken($user);
        if ($userToken === null) {
            return $this->errorResponse(self::INTERNAL_ERROR);
        }

        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('client_token' => $userToken->accept($apiVisistor)));
    }
}
