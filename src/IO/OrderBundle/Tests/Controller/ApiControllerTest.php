<?php

namespace IO\OrderBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class ApiControllerTest extends IOTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->truncate('IOOrderBundle:Order');
        $this->truncate('IOOrderBundle:OrderLine');
        $this->userExists('tablette', 'restaurantTest', 'ROLE_TABLETTE');
    }

    /**
     * @dataProvider orderAPIDataProvider
     */
    public function testOrderAPI($parameters, $expected)
    {
        $headers = array('HTTP_X_WSSE' => $this->generateWsseToken('tablette'));
        $this->client->request('GET', '/api/order', $parameters, array(), $headers);

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($expected, $result);
    }

    public function orderAPIDataProvider()
    {

        return array(
            array(
                array(),
                array('status' => false, 'message' => 'Empty command'),
            ),
        );
    }

}
