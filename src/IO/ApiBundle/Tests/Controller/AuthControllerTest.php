<?php

namespace IO\ApiBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class AuthControllerTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();
    }
    
    /**
     * @dataProvider authRestaurantDataProvider
     */
    public function testAuthRestaurant($data, $statusCode)
    {
        $this->getRestaurant('restaurant');
        $this->userExists('usertest', 'restaurant');
        $this->userExists('usertest2');

        $url = $this->container->get('router')->generate('api_restaurant_auth');
        $this->client->request('POST', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Data provider for test auth user
     * 
     * @return array
     */
    public function authRestaurantDataProvider()
    {
        return array(
            array(array(), 403),
            array(array('email' => 'usertest@io.fr', 'plainPassword' => 'badpwd'), 403),
            array(array('email' => 'usertest2@io.fr', 'plainPassword' => 'usertest2'), 403),
            array(array('email' => 'usertest@io.fr', 'plainPassword' => 'usertest'), 200),
        );
    }

}
