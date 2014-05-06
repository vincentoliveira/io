<?php

namespace IO\UserBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class ApiControllerTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();

        $user = $this->em->getRepository('IOUserBundle:User')->findOneByUsername('usertest');
        if ($user === null) {
            $user = new IO\UserBundle\Entity\User();
            $user->setUsername('usertest');
            $user->setEmail('usertest@io.fr');
            $user->setPlainPassword('usertest');
            $this->em->persist($user);
            $this->em->flush();
        }
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

    /**
     * Generate Wsse token
     * 
     * @param String $username
     * @param String $password
     * @param String $timestamp
     * @param String $nonce
     * @return String
     */
    private function generateWsseToken($username, $password = null, $timestamp = null, $nonce = null)
    {
        if ($password === null) {
            $user = $this->em->getRepository('IOUserBundle:User')->findOneByUsername($username);
            $password = $user->getPassword();
        }
        
        if ($timestamp === null) {
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        }
        if ($nonce === null) {
            $nonce = mt_rand();
        }

        $digest = base64_encode(sha1($nonce . $timestamp . $password, true));
        return sprintf('UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"', $username, $digest, base64_encode($nonce), $timestamp);
    }

}
