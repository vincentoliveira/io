<?php

namespace IO\OrderBundle\Tests\Controller;

use IO\DefaultBundle\Tests\IOTestCase;

class DefaultControllerTest extends IOTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/order/');

        $this->assertTrue($crawler->filter('html:contains("Hello World!")')->count() > 0);
    }
}
