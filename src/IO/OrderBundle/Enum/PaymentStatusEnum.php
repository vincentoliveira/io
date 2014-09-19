<?php

namespace IO\OrderBundle\Enum;

/**
 * Description of PaymentStatusEnum
 */
class PaymentStatusEnum
{

    const PAYMENT_CANCELED = 'CANCELED';
    const PAYMENT_FAILED = 'FAILED';
    const PAYMENT_PENDING = 'PENDING';
    const PAYMENT_SUCCESS = 'SUCCESS';

    static $allowedStatuses = array(
        self::PAYMENT_CANCELED,
        self::PAYMENT_FAILED,
        self::PAYMENT_PENDING,
        self::PAYMENT_SUCCESS,
    );
    
    public static function isValidStatus($status)
    {
        return in_array($status, self::$allowedStatuses);
    }

}
