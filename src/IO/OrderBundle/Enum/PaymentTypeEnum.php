<?php

namespace IO\OrderBundle\Enum;

/**
 * Description of PaymentTypeEnum
 */
class PaymentTypeEnum
{

    const PAYMENT_UNKNOWN = 'UNKNOWN';
    const PAYMENT_CASH = 'CASH';
    const PAYMENT_LYDIA = 'LYDIA';

    static $allowedType = array(
        self::PAYMENT_CASH,
        self::PAYMENT_LYDIA
    );

}
