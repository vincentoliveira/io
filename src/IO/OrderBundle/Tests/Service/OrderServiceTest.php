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

    public function testNextStatusForOrder()
    {
        $restaurant = $this->getRestaurant('restaurant');
        $orderSv = $this->container->get('io.order_service');
        $order = $orderSv->createOrder($restaurant);
        $this->assertEquals($order->getLastStatus(), OrderStatusEnum::STATUS_DRAFT);
        
        $orderSv->setNextStatusToOrder($order);
        $this->assertEquals($order->getLastStatus(), OrderStatusEnum::STATUS_INIT);
        
        $orderSv->setNextStatusToOrder($order);
        $this->assertEquals($order->getLastStatus(), OrderStatusEnum::STATUS_IN_PROGRESS);
        
        $orderSv->setNextStatusToOrder($order);
        $this->assertEquals($order->getLastStatus(), OrderStatusEnum::STATUS_CLOSED);
        
        $order2 = $orderSv->createOrder($restaurant);
        $this->cancelOrder($order2);

        $orderSv->setNextStatusToOrder($order2);
        $this->assertEquals($order2->getLastStatus(), OrderStatusEnum::STATUS_CANCELED);
    }
}
