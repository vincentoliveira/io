<?php

namespace IO\ApiBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;
use IO\OrderBundle\Entity\OrderData;
use IO\OrderBundle\Enum\PaymentStatusEnum;

class PaymentServiceTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @dataProvider handlePaymentDataProvider
     */
    public function testHandlePayment($input, $output)
    {
        $paymentSv = $this->container->get('io.payment_service');
        $payment = $paymentSv->handlePayment($input);

        $this->assertNotNull($payment);
        foreach ($output as $getter => $field) {
            $this->assertEquals($payment->{$getter}(), $field);
        }
    }

    /**
     * Data provider for test auth user
     * 
     * @return array
     */
    public function handlePaymentDataProvider()
    {
        return array(
            array(
                array(
                    'amount' => 10,
                    'fees_amount' => 1,
                    'type' => "TEST",
                    'status' => PaymentStatusEnum::PAYMENT_SUCCESS,
                ),
                array(
                    'getAmount' => 10,
                    'getFees' => 1,
                    'getType' => "TEST",
                    'getStatus' => PaymentStatusEnum::PAYMENT_SUCCESS,
                )
            ),
        );
    }

}
