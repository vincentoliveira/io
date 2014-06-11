<?php

namespace IO\OrderBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class ApiControllerTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->truncate('IOOrderBundle:OrderData');
        $this->truncate('IOOrderBundle:OrderLine');
        $this->truncate('IORestaurantBundle:CarteItem');
        $this->truncate('IORestaurantBundle:Media');
        $this->userExists('tablette', 'restaurantTest', 'ROLE_TABLETTE');

        $carte = array(
            'c1' => array(
                'restaurant' => 'restaurantTest',
                'visible' => true,
                'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY,
                'name' => 'Caterory1',
                'media' => 'aaa',
            ),
            'p1' => array(
                'restaurant' => 'restaurantTest',
                'visible' => true,
                'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_DISH,
                'name' => 'Dish1',
                'price' => 5,
                'vat' => 10,
                'parent' => 'c1',
                'media' => 'bbb',
            ),
            'p2' => array(
                'restaurant' => 'restaurantTest',
                'visible' => true,
                'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_DISH,
                'name' => 'Dish2',
                'price' => 10,
                'vat' => 10,
                'parent' => 'c1',
                'media' => 'bbb',
            ),
        );
        $this->insertCarteItems($carte);
    }

    /**
     * @dataProvider orderAPIDataProvider
     */
    public function testOrderAPI($parameters, $expected)
    {
        $headers = array('HTTP_X_WSSE' => $this->generateWsseToken('tablette'));
        $this->client->request('POST', '/api/order.json', array(), array(), $headers, json_encode($parameters));

        $result = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($expected, $result);
    }

    public function orderAPIDataProvider()
    {

        return array(
            array(
                array(),
                array('error' => 'Empty command'),
            ),
            array(
                'aaaaa',
                array('error' => 'Bad data'),
            ),
            array(
                array('items' => array('id' => 2)),
                array('order' => array(
                    'id' => 1,
                    'status' => 'WAITING',
                    'total_price' => 5,
                )),
            ),
        );
    }

}
