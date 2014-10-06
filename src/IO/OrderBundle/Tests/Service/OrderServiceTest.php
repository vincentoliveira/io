<?php

namespace IO\RestaurantBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;
use IO\OrderBundle\Enum\OrderStatusEnum;

class OrderServiceTest extends IOTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testCancelOrder()
    {
        $restaurant = $this->getRestaurant('restaurant');
        $orderSv = $this->container->get('io.order_service');
        $order = $orderSv->createOrder($restaurant);
        $this->assertNotEquals($order->getLastStatus(), OrderStatusEnum::STATUS_CANCELED);
        
        $orderSv->cancelOrder($order);
        $this->assertEquals($order->getLastStatus(), OrderStatusEnum::STATUS_CANCELED);
    }
}
