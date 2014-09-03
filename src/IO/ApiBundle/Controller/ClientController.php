<?php

namespace IO\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\ApiBundle\Utils\ApiElementVisitor;
use IO\ApiBundle\Utils\BadParameterException;

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
     * POST /client/create.json
     * 
     * Create a user from the json data post in the request
     * 
     * @param Request $request
     * @return JsonResponse
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

        $apiVisistor = new ApiElementVisitor();
        return new JsonResponse(array('user' => $apiVisistor->visitUser($user)));
    }

}
