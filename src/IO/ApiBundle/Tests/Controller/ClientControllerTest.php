<?php

namespace IO\ApiBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

/**
 * Client Controller Test
 */
class ClientControllerTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @dataProvider createClientDataProvider
     */
    public function testCreateClient($data, $statusCode)
    {
        $this->truncate('IOApiBundle:AuthToken');
        $this->userDoesNotExists('vincent@io.fr');
        $this->userExists('usertest');

        $url = $this->container->get('router')->generate('api_client_create');
        $this->client->request('POST', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Data provider for test auth user
     * 
     * @return array
     */
    public function createClientDataProvider()
    {
        return array(
            array(array(), 400),
            array(
                array(
                    'email' => 'vincent@io.fr',
                    'plainPassword' => 'test',
                    'gender' => 'MALE',
                    'lastname' => 'Oliveira',
                    'firstname' => 'Vincent',
                    'birthdate' => '06/10/1990',
                    'phone' => array(
                        array(
                            'prefix' => '+33',
                            'number' => '123456789',
                        ),
                    ),
                    'address' => array(
                        'number' => '39',
                        'street' => 'rue du Caire',
                        'postcode' => '75002',
                        'city' => 'Paris',
                        'country' => 'France',
                    ),
                ),
                200
            ),
        );
    }
    
    /**
     * @dataProvider authClientDataProvider
     */
    public function testAuthClient($data, $statusCode)
    {
        $this->clientExists('client');

        $url = $this->container->get('router')->generate('api_client_auth');
        $this->client->request('POST', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Data provider for test auth user
     * 
     * @return array
     */
    public function authClientDataProvider()
    {
        return array(
            array(array(), 403),
            array(array('email' => 'client@io.fr', 'plainPassword' => 'WRONG-PWD'), 403),
            array(array('email' => 'client@io.fr', 'plainPassword' => 'client'), 200),
        );
    }

}
