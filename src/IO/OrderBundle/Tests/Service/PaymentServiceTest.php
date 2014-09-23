<?php

namespace IO\RestaurantBundle\Tests\Controller;

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
            $this->assertEquals($payment->{$getter}(), $field, $getter);
        }
    }
    

    /**
     * @dataProvider handlePaymentDataMissingParamProvider
     * @expectedException \IO\ApiBundle\Utils\MissingParameterException
     */
    public function testHandlePaymentMissingParam($input)
    {
        $paymentSv = $this->container->get('io.payment_service');
        $paymentSv->handlePayment($input);
    }
    

    /**
     * @dataProvider handlePaymentDataBadParamProvider
     * @expectedException \IO\ApiBundle\Utils\BadParameterException
     */
    public function testHandlePaymentBadParam($input)
    {
        $paymentSv = $this->container->get('io.payment_service');
        $paymentSv->handlePayment($input);
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
            array(
                array(
                    'amount' => 10,
                    'fees_amount' => 1,
                    'type' => "TEST",
                    'status' => PaymentStatusEnum::PAYMENT_FAILED,
                    'date' => '2015-01-01 12:00:00',
                    'transaction_id' => '123456',
                    'comments' => base64_encode('This is a comment'),
                ),
                array(
                    'getAmount' => 10,
                    'getFees' => 1,
                    'getType' => "TEST",
                    'getStatus' => PaymentStatusEnum::PAYMENT_FAILED,
                    'getDate' => \DateTime::createFromFormat("Y-m-d H:i:s", '2015-01-01 12:00:00'),
                    'getTransactionId' => 123456,
                    'getComments' => 'This is a comment',
                )
            ),
        );
    }


    /**
     * Data provider for handle payment with missing param
     * 
     * @return array
     */
    public function handlePaymentDataMissingParamProvider()
    {
        return array(
            array(
                array(
                    'fees_amount' => 1,
                    'type' => "TEST",
                    'status' => PaymentStatusEnum::PAYMENT_SUCCESS,
                ),
            ),
            array(
                array(
                    'amount' => 10,
                    'type' => "TEST",
                    'status' => PaymentStatusEnum::PAYMENT_SUCCESS,
                ),
            ),
            array(
                array(
                    'amount' => 10,
                    'fees_amount' => 1,
                    'status' => PaymentStatusEnum::PAYMENT_SUCCESS,
                ),
            ),
            array(
                array(
                    'amount' => 10,
                    'fees_amount' => 1,
                    'type' => "TEST",
                ),
            ),
        );
    }
    

    /**
     * Data provider for handle payment with bad param
     * 
     * @return array
     */
    public function handlePaymentDataBadParamProvider()
    {
        return array(
            array(
                array(
                    'amount' => 10,
                    'fees_amount' => 1,
                    'type' => "TEST",
                    'status' => "THIS_STATUS_DOES_NOT_EXIST",
                ),
            ),
        );
    }
}
