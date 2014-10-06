<?php

namespace IO\ApiBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;
use IO\OrderBundle\Enum\OrderStatusEnum;

class OrderControllerTest extends IOTestCase
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
        $order = $this->createCart($restaurant, $token);
        $order2 = $this->createCart($restaurant2, $token2);
        
        $this->data['restaurant'] = $restaurant->getId();
        $this->data['restaurant2'] = $restaurant2->getId();
        $this->data['token'] = $token->getToken();
        $this->data['token2'] = $token2->getToken();
        $this->data['order'] = $order->getId();
        $this->data['order2'] = $order2->getId();
        
        $this->data['entity_restaurant'] = $restaurant;
        $this->data['entity_restaurant2'] = $restaurant2;
        $this->data['entity_token'] = $token;
        $this->data['entity_token2'] = $token2;
        $this->data['entity_order'] = $order;
        $this->data['entity_order2'] = $order2;
    }
    
    public function testCancelOK()
    {
        $url = $this->container->get('router')->generate('api_order_cancel', array('id' => $this->data['order']));
        $data = array(
            'restaurant_id' => $this->data['restaurant'],
            'restaurant_token' => $this->data['token'],
        );
        $this->client->request('PUT', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('order', $result);
        $this->assertEquals($result['order']['status'], \IO\OrderBundle\Enum\OrderStatusEnum::STATUS_CANCELED);
    }
    
    public function testCancelKO403()
    {
        $url = $this->container->get('router')->generate('api_order_cancel', array('id' => $this->data['order']));
        $data = array(
            'restaurant_id' => $this->data['restaurant2'],
            'restaurant_token' => $this->data['token'],
        );
        $this->client->request('PUT', $url, $data);
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }
       
    public function testCancelKO404()
    { 
        $url = $this->container->get('router')->generate('api_order_cancel', array('id' => $this->data['order2']));
        $data = array(
            'restaurant_id' => $this->data['restaurant'],
            'restaurant_token' => $this->data['token'],
        );
        $this->client->request('PUT', $url, $data);
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
       
    public function testCancelKOClosed()
    {
        $this->cancelOrder($this->data['entity_order']);
        $url = $this->container->get('router')->generate('api_order_cancel', array('id' => $this->data['order']));
        $data = array(
            'restaurant_id' => $this->data['restaurant'],
            'restaurant_token' => $this->data['token'],
        );
        $this->client->request('PUT', $url, $data);
        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }


    public function testNextStatusOK()
    {
        $url = $this->container->get('router')->generate('api_order_next_status', array('id' => $this->data['order']));
        $data = array(
            'restaurant_id' => $this->data['restaurant'],
            'restaurant_token' => $this->data['token'],
        );
        $this->client->request('PUT', $url, $data);

        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $result = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('order', $result);
        $this->assertEquals($result['order']['status'], \IO\OrderBundle\Enum\OrderStatusEnum::STATUS_INIT);
    }
    
    public function testNextStatusKO403()
    {
        $url = $this->container->get('router')->generate('api_order_next_status', array('id' => $this->data['order']));
        $data = array(
            'restaurant_id' => $this->data['restaurant2'],
            'restaurant_token' => $this->data['token'],
        );
        $this->client->request('PUT', $url, $data);
        $response = $this->client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }
       
    public function testNextStatusKO404()
    { 
        $url = $this->container->get('router')->generate('api_order_next_status', array('id' => $this->data['order2']));
        $data = array(
            'restaurant_id' => $this->data['restaurant'],
            'restaurant_token' => $this->data['token'],
        );
        $this->client->request('PUT', $url, $data);
        $response = $this->client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
       
    public function testNextStatusClosed()
    {
        $this->cancelOrder($this->data['entity_order']);
        $url = $this->container->get('router')->generate('api_order_next_status', array('id' => $this->data['order']));
        $data = array(
            'restaurant_id' => $this->data['restaurant'],
            'restaurant_token' => $this->data['token'],
        );
        $this->client->request('PUT', $url, $data);
        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
    }
    
    
}
