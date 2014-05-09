<?php

namespace IO\RestaurantBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class ApiControllerTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();
        
        $this->userExists('tablette', 'restaurantTest', 'ROLE_TABLETTE');
    }

    /**
     * @dataProvider carteWSDataProvider
     */
    public function testCarteWS($data, $expected)
    {
        $headers = array('HTTP_X_WSSE' => $this->generateWsseToken('tablette'));
        $this->client->request('GET', '/api/restaurant/carte', array(), array(), $headers);
        
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($expected, $result);
    }
    
    public function carteWSDataProvider()
    {
        return array(
            array(
                array(),
                array('carte' => array()),
            ),
        );
    }

}
