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
                    'lastname' => 'Oliveira',
                    'firstname' => 'Vincent',
                    'birthdate' => '1990-06-10',
                ),
                200
            ),
            array(
                array(
                    'email' => 'vincent@io.fr',
                    'plainPassword' => 'test',
                    'gender' => 'MALE',
                    'lastname' => 'Oliveira',
                    'firstname' => 'Vincent',
                    'birthdate' => '1990-06-10',
                    'nationality' => 'FR',
                    'phones' => array(
                        array(
                            'prefix' => '+33',
                            'number' => '123456789',
                        ),
                    ),
                    'addresses' => array(
                        array(
                            'number' => '39',
                            'street' => 'rue du Caire',
                            'postcode' => '75002',
                            'city' => 'Paris',
                            'country' => 'FR',
                        ),
                    ),
                    'wallet' => array(
                        'user_id' => 12345,
                        'wallet_id' => 67890,
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
    
    
    /**
     * @dataProvider editClientDataProvider
     */
    public function testEditClient($data, $statusCode)
    {
        $this->truncate('IOApiBundle:AuthToken');
        $this->clientExists('client');

        $user = $this->em->getRepository('IOUserBundle:User')->findOneByEmail('client@io.fr');
        $url = $this->container->get('router')->generate('api_client_edit', array('id' => $user->getId()));
        $this->client->request('PUT', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Data provider for test auth user
     * 
     * @return array
     */
    public function editClientDataProvider()
    {
        return array(
            array(array(), 400),
            array(
                array(
                    'email' => 'vincent@io.fr',
                    'plainPassword' => 'test',
                    'lastname' => 'Oliveira',
                    'firstname' => 'Vincent',
                    'birthdate' => '1990-06-10',
                    'phones' => array(
                        array(
                            'prefix' => '+33',
                            'number' => '123456789',
                        ),
                    ),
                    'addresses' => array(
                        array(
                            'number' => '39',
                            'street' => 'rue du Caire',
                            'postcode' => '75002',
                            'city' => 'Paris',
                            'country' => 'FR',
                        ),
                    ),
                    'wallet' => array(
                        'user_id' => 12345,
                        'wallet_id' => 67890,
                    ),
                ),
                200,
            ),
            array(
                array(
                    'lastname' => 'Oliveira',
                    'firstname' => 'Vincent',
                ),
                200,
            ),
        );
    }
}
