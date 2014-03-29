<?php

namespace IO\CarteBundle\Tests\Services;

use IO\CarteBundle\Tests\IOTestCase;

/**
 * Description of DishOptionServiceTest
 *
 * @author Vincent
 */
class DishOptionServiceTest extends IOTestCase
{
    /**
     * @var \IO\CarteBundle\Services\DishOptionService
     */
    protected $service;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->service = $this->container->get('menu.dish_option');
        $this->truncate("IOCarteBundle:DishOption");
    }
    
    public function testGetListEmpty()
    {
        $list = $this->service->getPaginatedList();
        
        $this->assertSame(array(), $list);
    }
}
