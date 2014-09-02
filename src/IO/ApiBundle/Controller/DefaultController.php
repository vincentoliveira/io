<?php

namespace IO\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use IO\RestaurantBundle\Entity\Restaurant;

/**
 * API default Controller
 */
abstract class DefaultController extends Controller
{
    const UNKNOWN_ERROR = "UNKNOWN_ERROR";
    const INTERNAL_ERROR = "INTERNAL_ERROR";
    const EMPTY_PARAMETER = "EMPTY_PARAMETER";
    const BAD_PARAMETER = "BAD_PARAMETER";
    const BAD_AUTHENTIFICATION = "BAD_AUTHENTIFICATION";
    const ORDER_LOCKED = "ORDER_LOCKED";
    
    /**
     * @var \IO\ApiBundle\Entity\AuthToken 
     */
    protected $authToken = null;

    static private $error_data = array(
        self::UNKNOWN_ERROR => array(
            'error_code' => 200,
            'message' => "An unexpected error has occured.",
            'err_no' => -1,
        ),
        self::INTERNAL_ERROR => array(
            'error_code' => 500,
            'message' => "An unexpected error has occured.",
            'err_no' => 0,
        ),
        self::EMPTY_PARAMETER => array(
            'error_code' => 400,
            'message' => "Empty parameter.",
            'err_no' => 1,
        ),
        self::BAD_PARAMETER => array(
            'error_code' => 400,
            'message' => "Bad parameter.",
            'err_no' => 2,
        ),
        self::BAD_AUTHENTIFICATION => array(
            'error_code' => 403,
            'message' => "Bad authentification.",
            'err_no' => 3,
        ),
        self::ORDER_LOCKED => array(
            'error_code' => 400,
            'message' => "This order is locked.",
            'err_no' => 4,
        ),
    );


    protected function errorResponse($errorID = self::UNKNOWN_ERROR, $msg = '')
    {
        if (isset(self::$error_data[$errorID])) {
            $error_data = self::$error_data[$errorID];
        } else {
            $error_data = self::$error_data[self::UNKNOWN_ERROR];
        }
        
        return new JsonResponse(array(
            'error' => $error_data['err_no'],
            'message' => empty($msg) ? $error_data['message'] : $msg,
        ), $error_data['error_code']);
    }

    /**
     * Check authentification token for restaurant
     * 
     * @param string $token
     * @param Restaurant $restaurant
     * @return boolean
     */
    protected function checkToken($token, Restaurant $restaurant = null)
    {
        if (empty($token)) {
            return false;
        }
        
        $em = $this->getDoctrine()->getManager();
        $authTokenRepo = $em->getRepository("IOApiBundle:AuthToken");
        $this->authToken = $authTokenRepo->findOneByToken($token);        
        
        return $this->authToken !== null && !$this->authToken->hasExpired() &&
                ($restaurant === null || $this->authToken->getRestrictedRestaurants()->contains($restaurant));
    }
}
