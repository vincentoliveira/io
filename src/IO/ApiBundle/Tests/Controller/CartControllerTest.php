<?php

namespace IO\ApiBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;
use Symfony\Component\HttpFoundation\Request;

class CartControllerTest extends IOTestCase
{

    protected $data = array();

    public function setUp()
    {
        parent::setUp();

        $this->truncate('IOOrderBundle:OrderData');
        $this->truncate('IOOrderBundle:OrderLine');
        $this->truncate('IORestaurantBundle:CarteItem');

        $restaurant = $this->getRestaurant('test');
        $restaurant2 = $this->getRestaurant('test2');
        $token = $this->getTokenForRestaurant($restaurant);
        $token2 = $this->getTokenForRestaurant($restaurant2);
        $product = $this->productExistInCategoryForRestaurant('product', 'category', $restaurant);
        $product2 = $this->productExistInCategoryForRestaurant('product2', 'category', $restaurant);
        $option = $this->createOptionForProduct('option', array('choice1', 'choice2'), $product);
        
        
        $this->data['restaurant'] = $restaurant->getId();
        $this->data['restaurant2'] = $restaurant2->getId();
        $this->data['token'] = $token->getToken();
        $this->data['token2'] = $token2->getToken();
        $this->data['product'] = $product->getId();
        $this->data['product2'] = $product2->getId();
        $this->data['options'] = array($option->getChildren()->first()->getId());
        
        $this->data['entity_restaurant'] = $restaurant;
        $this->data['entity_restaurant2'] = $restaurant2;
        $this->data['entity_token'] = $token;
        $this->data['entity_token2'] = $token2;
        $this->data['entity_product'] = $product;
        $this->data['entity_product2'] = $product2;
    }
    
    /**
     * @dataProvider getCartDataProvider
     */
    public function testGetCart($data, $statusCode, $expected)
    {
        foreach ($data as $key => $value) {
            if (isset($this->data[$value])) {
                $data[$key] = $this->data[$value];
            }
        }

        $cart = $this->createCart($this->data['entity_restaurant'], $this->data['entity_token']);
        $url = $this->container->get('router')->generate('api_order_get_cart', array('cartId' => $cart->getId()));
        $this->client->request('GET', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        $this->assertEquals($expected, $result);
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
     * @dataProvider addProductToCartDataProvider
     */
    public function testAddProductToCart($data, $statusCode, $expected)
    {
        foreach ($data as $key => $value) {
            if (isset($this->data[$value])) {
                $data[$key] = $this->data[$value];
            }
        }

        $cart = $this->createCart($this->data['entity_restaurant'], $this->data['entity_token']);
        $data['cart_id'] = $cart->getId();

        $url = $this->container->get('router')->generate('api_order_add_product_to_cart');
        $this->client->request('POST', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider removeProductFromCartDataProvider
     */
    public function testRemoveProductFromCart($data, $statusCode, $expected)
    {
        foreach ($data as $key => $value) {
            if (isset($this->data[$value])) {
                $data[$key] = $this->data[$value];
            }
        }

        $cart = $this->createCart($this->data['entity_restaurant'], $this->data['entity_token']);
        $this->addProductToCart($this->data['entity_product'], $cart);
        $this->addProductToCart($this->data['entity_product'], $cart);
        $data['cart_id'] = $cart->getId();

        $url = $this->container->get('router')->generate('api_order_remove_product_from_cart');
        $this->client->request('DELETE', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals($statusCode, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Data provider for test get cart
     * 
     * @return array
     */
    public function getCartDataProvider()
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
                        "no_tax_total" => 0,
                        "vat_amount" => 0,
                        "total_unpayed" => 0,
                    ),
                ),
            ),
        );
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
                        "no_tax_total" => 0,
                        "vat_amount" => 0,
                        "total_unpayed" => 0,
                    ),
                ),
            ),
            array(
                array(
                    'token' => 'token',
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
                        "no_tax_total" => 0,
                        "vat_amount" => 0,
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
                                "vat" => 20.00,
                                "price" => 1.20,
                            ),
                        ),
                        "payments" => array(),
                        "total" => 1.20,
                        "no_tax_total" => 1.00,
                        "vat_amount" => 0.20,
                        "total_unpayed" => 1.2,
                    ),
                ),
            ),
        );
    }

    /**
     * Data provider for add product to cart
     * 
     * @return array
     */
    public function addProductToCartDataProvider()
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
                400,
                array(
                    'error' => 2,
                    'message' => 'Bad parameter.',
                ),
            ),
            array(
                array(
                    'token' => 'token',
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
                                "vat" => 20.00,
                                "price" => 1.20,
                            ),
                        ),
                        "payments" => array(),
                        "total" => 1.20,
                        "no_tax_total" => 1.00,
                        "vat_amount" => 0.20,
                        "total_unpayed" => 1.2,
                    ),
                ),
            ),
            array(
                array(
                    'token' => 'token',
                    'product_id' => 'product',
                    'options' => 'options',
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
                                "extra" => "choice1",
                                "vat" => 20.00,
                                "price" => 1.20,
                            ),
                        ),
                        "payments" => array(),
                        "total" => 1.20,
                        "no_tax_total" => 1.00,
                        "vat_amount" => 0.20,
                        "total_unpayed" => 1.2,
                    ),
                ),
            ),
        );
    }

    /**
     * Data provider for add product to cart
     * 
     * @return array
     */
    public function removeProductFromCartDataProvider()
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
                400,
                array(
                    'error' => 2,
                    'message' => 'Bad parameter.',
                ),
            ),
            array(
                array(
                    'token' => 'token',
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
                                "vat" => 20.00,
                                "price" => 1.20,
                            ),
                        ),
                        "payments" => array(),
                        "total" => 1.20,
                        "no_tax_total" => 1.00,
                        "vat_amount" => 0.20,
                        "total_unpayed" => 1.2,
                    ),
                ),
            ),
            array(
                array(
                    'token' => 'token',
                    'product_id' => 'product2',
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
                                "vat" => 20.00,
                                "price" => 1.20,
                            ),
                            array(
                                "product_id" => 2,
                                "name" => "product",
                                "short_name" => "product",
                                "extra" => "",
                                "vat" => 20.00,
                                "price" => 1.20,
                            ),
                        ),
                        "payments" => array(),
                        "total" => 2.40,
                        "no_tax_total" => 2.00,
                        "vat_amount" => 0.40,
                        "total_unpayed" => 2.4,
                    ),
                ),
            ),
        );
    }

}
