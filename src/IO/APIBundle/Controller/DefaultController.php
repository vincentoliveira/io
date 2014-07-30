<?php

namespace IO\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * API default Controller
 */
class DefaultController extends Controller
{
    const UNKNOWN_ERROR = "UNKNOWN_ERROR";
    const EMPTY_PARAMETER = "EMPTY_PARAMETER";
    const BAD_PARAMETER = "BAD_PARAMETER";

    static private $error_data = array(
        self::UNKNOWN_ERROR => array(
            'error_code' => 200,
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
            'err_no' => 1,
        ),
    );


    protected function errorResponse($errorID = self::UNKNOWN_ERROR)
    {
        if (isset(self::$error_data[$errorID])) {
            $error_data = self::$error_data[$errorID];
        } else {
            $error_data = self::$error_data[self::UNKNOWN_ERROR];
        }
        
        return new JsonResponse(array(
            'error' => $error_data['err_no'],
            'message' => $error_data['message'],
        ), $error_data['error_code']);
    }

}
