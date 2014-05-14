<?php

namespace IO\RestaurantBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class ApiControllerTest extends IOTestCase
{

    /**
     * @var \IO\RestaurantBundle\Entity\Restaurant
     */
    protected $restaurant;

    public function setUp()
    {
        parent::setUp();

        $this->truncate('IORestaurantBundle:CarteItem');
        $this->truncate('IORestaurantBundle:Media');
        $this->userExists('tablette', 'restaurantTest', 'ROLE_TABLETTE');
    }

    /**
     * @dataProvider carteWSDataProvider
     */
    public function testCarteWS($data, $expected)
    {
        $this->insertCarteItems($data);

        $headers = array('HTTP_X_WSSE' => $this->generateWsseToken('tablette'));
        $this->client->request('GET', '/api/carte.json', array(), array(), $headers);

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
            array(
                array(
                    'c1' => array(
                        'restaurant' => 'restaurantTest',
                        'visible' => false,
                        'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY,
                        'name' => 'Caterory1',
                    ),
                    'p1' => array(
                        'restaurant' => 'restaurantTest',
                        'visible' => false,
                        'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_DISH,
                        'name' => 'Dish1',
                        'price' => 5,
                        'vat' => 10,
                        'parent' => 'c1',
                    ),
                ),
                array('carte' => array()),
            ),
            array(
                array(
                    'c1' => array(
                        'restaurant' => 'restaurantTest',
                        'visible' => true,
                        'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY,
                        'name' => 'Caterory1',
                    ),
                    'p1' => array(
                        'restaurant' => 'restaurantTest',
                        'visible' => true,
                        'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_DISH,
                        'name' => 'Dish1',
                        'price' => 5,
                        'vat' => 10,
                        'parent' => 'c1',
                    ),
                ),
                array('carte' => array(
                        array(
                            'id' => 1,
                            'name' => 'Caterory1',
                            'description' => '',
                            'type' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY,
                            'children' => array(
                                array(
                                    'id' => 2,
                                    'name' => 'Dish1',
                                    'description' => '',
                                    'type' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_DISH,
                                    'price' => 5,
                                    'vat' => 10,
                                ),
                            ),
                        )
                )),
            ),
            array(
                array(
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
                ),
                array('carte' => array(
                        array(
                            'id' => 1,
                            'name' => 'Caterory1',
                            'description' => '',
                            'type' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY,
                            'media' => array(
                                'id' => 1,
                                'path' => 'aaa',
                            ),
                            'children' => array(
                                array(
                                    'id' => 2,
                                    'name' => 'Dish1',
                                    'description' => '',
                                    'type' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_DISH,
                                    'price' => 5,
                                    'vat' => 10,
                                    'media' => array(
                                        'id' => 2,
                                        'path' => 'bbb',
                                    ),
                                ),
                            ),
                        )
                )),
            ),
        );
    }

}
