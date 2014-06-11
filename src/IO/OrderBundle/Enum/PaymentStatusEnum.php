<?php

namespace IO\OrderBundle\Enum;

/**
 * Description of PaymentStatusEnum
 */
class PaymentStatusEnum
{

    const PAYMENT_CANCELED = 'CANCELED';
    const PAYMENT_ERROR = 'ERROR';
    const PAYMENT_PENDING = 'PENDING';
    const PAYMENT_SUCCESS = 'SUCCESS';

    static $allowedStatuses = array(
        self::PAYMENT_CANCELED,
        self::PAYMENT_ERROR,
        self::PAYMENT_SUCCESS,
        self::PAYMENT_PENDING
    );

}
