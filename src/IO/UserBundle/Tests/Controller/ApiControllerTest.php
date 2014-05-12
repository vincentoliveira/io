<?php

namespace IO\UserBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class ApiControllerTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->userExists('usertest');
    }
    
    public function testGetSaltUserDoesNotExist()
    {
        $this->userDoesNotExists('usertesttest');
        
        $expected = array('status' => 'ko');
        $this->client->request('GET', '/api/salt?username=usertesttest');
        
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($expected, $result);
    }
    
    public function testGetSaltUserExists()
    {
        $user = $this->em->getRepository('IOUserBundle:User')->findOneByUsername('usertest');
        $expected = array('status' => 'ok', 'salt' => $user->getSalt());
        $this->client->request('GET', '/api/salt?username=usertest');
        
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($expected, $result);
    }
    
    public function testWsseAuthNoToken()
    {
        $expected = array('status' => 'ok', 'login' => false, 'reason' => 'Bad token');
        $this->client->request('GET', '/api/check_login');
        
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider wsseHeaderDataProvider
     */
    public function testWsseAuth($expected, $password = null, $timestamp = null, $nonce = null)
    {
        $headers = array('HTTP_X_WSSE' => $this->generateWsseToken('usertest', $password, $timestamp, $nonce));
        $this->client->request('GET', '/api/check_login', array(), array(), $headers);
        
        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($expected, $result);
    }
    
    public function wsseHeaderDataProvider()
    {
        $nonUniqNonce = mt_rand();
        return array(
            array(
                array('status' => 'ok', 'login' => true),
            ),
            array(
                array('status' => 'ok', 'login' => false, 'reason' => 'Bad token'),
                'aze',
            ),
            array(
                array('status' => 'ok', 'login' => false, 'reason' => 'Bad token'),
                null,
                '2012-01-01T12:34:56Z',
            ),
            array(
                array('status' => 'ok', 'login' => true),
                null,
                null,
                $nonUniqNonce,
            ),
            array(
                array('status' => 'ok', 'login' => false, 'reason' => 'Bad token'),
                null,
                null,
                $nonUniqNonce,
            ),
        );
    }

}
