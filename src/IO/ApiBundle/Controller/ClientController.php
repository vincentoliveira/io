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
            $phones = array('phone1' => null, 'phone2' => null);
            $addresses = array('address1' => null, 'address2' => null, 'address3' => null);
            $wallet = null;

            $i = 0;
            foreach ($phones as $key => $value) {
                if (isset($data['phones'][$i])) {
                    $phones[$key] = $this->userSv->createPhoneNumber($data['phones'][$i]);
                }
                $i++;
            }
            $i = 0;
            foreach ($addresses as $key => $value) {
                if (isset($data['addresses'][$i])) {
                    $addresses[$key] = $this->userSv->createAddress($data['addresses'][$i]);
                }
                $i++;
            }
                
            if (isset($data['wallet'])) {
                $wallet = $this->userSv->createWallet($data['wallet']);
            }
        } catch (\Exception $e) {
            // skip error
        }

        try {
            $em = $this->getDoctrine()->getManager();
            
            $client = $this->userSv->createUser($data);
            $identity = $this->userSv->createUserIdentity($data);
            
            $idFields = array_merge($phones, $addresses);
            foreach ($idFields as $field => $value) {
                if ($value) {
                    $setter = 'set' . ucfirst($field);
                    $identity->{$setter}($value);
                    $em->persist($value);
                }
            }
            if ($wallet !== null) {
                $client->setWallet($wallet);
                $em->persist($wallet);
            }
            
            $client->setIdentity($identity);
            $em->persist($identity);
            $em->persist($client);
            $em->flush();
        } catch (BadParameterException $e) {
            return $this->errorResponse(self::BAD_PARAMETER, $e->getMessage());
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return $this->errorResponse(self::INTERNAL_ERROR, $e->getMessage());
        }
        
        $userToken = $this->userTokenSv->createToken($client);

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
        if ($data === null || empty($data) ||
                !isset($data['email']) || !isset($data['plainPassword'])) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }

        $user = $this->userSv->authUserData($data['email'], $data['plainPassword']);
        if ($client === null || $client->getIdentity() === null) {
            return $this->errorResponse(self::BAD_AUTHENTIFICATION);
        }
        
        $userToken = $this->userTokenSv->createToken($client);
        if ($userToken === null) {
            return $this->errorResponse(self::INTERNAL_ERROR);
        }

        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('client_token' => $userToken->accept($apiVisistor)));
    }
    
    
    /**
     * POST /client/edit/{user-id}.json
     * 
     * Create a user from the json data post in the request.
     * Return user data.
     * 
     * Parameters:
     * - <strong>email</strong> Email of the user (string)
     * - <strong>plainPassword</strong> Plain password of the user (string)
     * - <strong>firstname</strong> Firstname of the user (string)
     * - <strong>lastname</strong> Lastname of the user (string)
     * - <strong>birthdate</strong> Birthdate of the user [Y-m-d] (string)
     * 
     * @Route("/edit/{id}.json", name="api_client_edit")
     * @ParamConverter("user", class="IOUserBundle:User")
     * @Method("PUT")
     */
    public function editAction(User $client, Request $request)
    {
        $data = $request->request->all();
        if ($data === null || empty($data)) {
            return $this->errorResponse(self::EMPTY_PARAMETER);
        }

        try {
            $identity = $this->userSv->editUserIdentity($client->getIdentity(), $data);
            $phones = array(
                'phone1' => $identity->getPhone1(),
                'phone2' => $identity->getPhone2(),
            );
            $addresses = array(
                'address1' => $identity->getAddress1(),
                'address2' => $identity->getAddress2(),
                'address3' => $identity->getAddress3(),
            );
            
            $i = 0;
            foreach ($phones as $key => $value) {
                if (isset($data['phones'][$i])) {
                    $phones[$key] = $this->userSv->editPhoneNumber($value, $data['phones'][$i], false);
                }
                $i++;
            }
            $i = 0;
            foreach ($addresses as $key => $value) {
                if (isset($data['addresses'][$i])) {
                    $addresses[$key] = $this->userSv->editAddress($value, $data['addresses'][$i], false);
                }
                $i++;
            }
            
            $wallet = null;
            if (isset($data['wallet'])) {
                $wallet = $this->userSv->editWallet($client->getWallet(), $data['wallet']);
            }
            
            $em = $this->getDoctrine()->getManager();
            $idFields = array_merge($phones, $addresses);
            foreach ($idFields as $field => $value) {
                if ($value) {
                    $setter = 'set' . ucfirst($field);
                    $identity->{$setter}($value);
                    $em->persist($value);
                }
            }
            if ($wallet !== null) {
                $client->setWallet($wallet);
                $em->persist($wallet);
            }
            
            $client->setIdentity($identity);
            $em->persist($identity);
            $em->persist($client);
            $em->flush();
        } catch (BadParameterException $e) {
            return $this->errorResponse(self::BAD_PARAMETER, $e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResponse(self::INTERNAL_ERROR, $e->getMessage());
        }
        
        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('client' => $client->accept($apiVisistor)));
    }
}
