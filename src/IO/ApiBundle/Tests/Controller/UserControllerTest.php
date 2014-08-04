<?php

namespace IO\ApiBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @dataProvider createUserDataProvider
     */
    public function testCreateUser($data, $statusCode, $expected)
    {
        $this->truncate('IOUserBundle:User');
        $this->userExists('usertest2');

        $url = $this->container->get('router')->generate('api_user_create');
        $serveur = array(
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        );
        $this->client->request('POST', $url, [], [], $serveur, json_encode($data));

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test create user
     * 
     * @return array
     */
    public function createUserDataProvider()
    {
        return array(
            array(array(), 400, array('error' => 1, 'message' => 'Empty parameter.')),
            array(array('username' => 'usertest'), 400, array('error' => 2, 'message' => 'Bad parameter.')),
            array(array('username' => 'usertest2', 'plainPassword' => 'usertest2', 'email' => 'email@test.fr'), 400, array('error' => 2, 'message' => 'Bad parameter.')),
            array(array('username' => 'usertest', 'plainPassword' => 'usertest', 'email' => 'usertest2@io.fr'), 400, array('error' => 2, 'message' => 'Bad parameter.')),
            array(array('username' => 'usertest', 'plainPassword' => 'usertest', 'email' => 'email@test.fr'), 200, array('user' => array('id' => 2, 'username' => 'usertest', 'email' => 'email@test.fr'))),
        );
    }
    
    /**
     * @dataProvider authUserDataProvider
     */
    public function testAuthUser($data, $statusCode)
    {
        $this->userExists('usertest');

        $url = $this->container->get('router')->generate('api_user_auth');
        $serveur = array(
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        );
        $this->client->request('POST', $url, [], [], $serveur, json_encode($data));

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
    }

    /**
     * Data provider for test auth user
     * 
     * @return array
     */
    public function authUserDataProvider()
    {
        return array(
            array(array(), 403),
            array(array('email' => 'usertest@io.fr', 'plainPassword' => 'badpwd'), 403),
            array(array('email' => 'usertest@io.fr', 'plainPassword' => 'usertest'), 200),
        );
    }

}
