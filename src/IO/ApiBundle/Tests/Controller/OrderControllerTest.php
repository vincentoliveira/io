<?php

namespace IO\ApiBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;
use Symfony\Component\HttpFoundation\Request;

class OrderControllerTest extends IOTestCase
{

    protected $data = array();

    public function setUp()
    {
        parent::setUp();

        $this->truncate('IOOrderBundle:OrderData');
        $this->truncate('IORestaurantBundle:CarteItem');

        $restaurant = $this->getRestaurant('test');
        $restaurant2 = $this->getRestaurant('test2');
        $token = $this->getTokenForRestaurant($restaurant);
        $token2 = $this->getTokenForRestaurant($restaurant2);
        $product = $this->productExistInCategoryForRestaurant('product', 'category', $restaurant);
        $this->data['restaurant'] = $restaurant->getId();
        $this->data['restaurant2'] = $restaurant2->getId();
        $this->data['token'] = $token->getToken();
        $this->data['token2'] = $token2->getToken();
        $this->data['product'] = $product->getId();
    }

    /**
     * @dataProvider createCartDataProvider
     */
    public function testCreateCart($data, $statusCode, $expected)
    {
        foreach ($data as $key => $value) {
            if (isset($this->data[$value])) {
                $data[$key] = $this->data[$value];
            }
        }

        $url = $this->container->get('router')->generate('api_order_create_cart');
        $this->client->request('POST', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for test create cart
     * 
     * @return array
     */
    public function createCartDataProvider()
    {
        return array(
            array(
                array(),
                403,
                array(
                    'error' => 3,
                    'message' => 'Bad authentification.',
                ),
            ),
            array(
                array(
                    'token' => 'token2',
                    'restaurant_id' => 'restaurant',
                ),
                403,
                array(
                    'error' => 3,
                    'message' => 'Bad authentification.',
                ),
            ),
            array(
                array(
                    'token' => 'token',
                    'restaurant_id' => 'restaurant',
                ),
                200,
                array(
                    'cart' => array(
                        'id' => 1,
                        "delevery_date" => null,
                        "status" => "DRAFT",
                        "customer" => null,
                        "products" => array(),
                        "payments" => array(),
                        "total" => 0,
                        "total_unpayed" => 0,
                    ),
                ),
            ),
            array(
                array(
                    'token' => 'token',
                    'restaurant_id' => 'restaurant',
                    'product_id' => 'product',
                ),
                200,
                array(
                    'cart' => array(
                        'id' => 1,
                        "delevery_date" => null,
                        "status" => "DRAFT",
                        "customer" => null,
                        "products" => array(
                            array(
                                "product_id" => 2,
                                "name" => "product",
                                "short_name" => "product",
                                "extra" => "",
                                "vat" => "20.00",
                                "price" => 1,
                            ),
                        ),
                        "payments" => array(),
                        "total" => 1,
                        "total_unpayed" => 1,
                    ),
                ),
            ),
        );
    }

}
