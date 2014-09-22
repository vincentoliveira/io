<?php

namespace IO\UserBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;


class UserServiceTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @dataProvider authUserDataDataProvider
     */
    public function testAuthUserData($email, $plainPassword, $isExpected)
    {
        $restaurant = $this->getRestaurant('restaurant');
        $userExpected = $this->userExists('restaurant', 'restaurant');
        
        $userSv = $this->container->get('io.user_service');
        $user = $userSv->authUserData($email, $plainPassword);

        if ($isExpected) {
            $this->assertEquals($user, $userExpected);
            $this->assertEquals($user->getRestaurant(), $restaurant);
        } else {
            $this->assertNull($user);
        }
    }
    /**
     * Data provider for test auth user
     * 
     * @return array
     */
    public function authUserDataDataProvider()
    {
        return array(
            array(
                'restaurant@io.fr',
                'restaurant',
                true
            ),
            array(
                'baduser@io.fr',
                'restaurant',
                false
            ),
            array(
                'restaurant@io.fr',
                'badpassword',
                false
            ),
        );
    }

}
