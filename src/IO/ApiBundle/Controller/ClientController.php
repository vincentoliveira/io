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
            $phone2 = $address1 = $address2 = $address3 = null;
            if (isset($data['phones'][1])) {
                $phone2 = $this->userSv->createPhoneNumber($data['phones'][1]);
            }
            if (isset($data['addresses'][0])) {
                $address1 = $this->userSv->createAddress($data['addresses'][0]);
            }
            if (isset($data['addresses'][1])) {
                $address2 = $this->userSv->createAddress($data['addresses'][1]);
            }
            if (isset($data['addresses'][2])) {
                $address3 = $this->userSv->createAddress($data['addresses'][2]);
            }
        } catch (\Exception $e) {
            // skip error
        }

        try {
            $em = $this->getDoctrine()->getManager();
            
            $identity = $this->userSv->createUserIdentity($data);
            $em->persist($identity);
            
            if (isset($data['phones'][0])) {
                $phone1 = $this->userSv->createPhoneNumber($data['phones'][0]);
                $identity->setPhone1($phone1);
                $em->persist($phone1);
            }
            if ($phone2 !== null) {
                $identity->setPhone2($phone2);
                $em->persist($phone2);
            }
            if ($address1 !== null) {
                $identity->setAddress1($address1);
                $em->persist($address1);
            }
            if ($address2 !== null) {
                $identity->setAddress2($address2);
                $em->persist($address2);
            }
            if ($address3 !== null) {
                $identity->setAddress3($address3);
                $em->persist($address3);
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
