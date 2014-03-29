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
    
    /**
     * @var \IO\CarteBundle\Entity\Restaurant
     */
    protected $restaurant;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->service = $this->container->get('menu.dish_option');
        $this->truncate("IOCarteBundle:DishOption");
        $this->restaurant = $this->getRestaurantTest();
    }
    
    public function testGetListEmpty()
    {
        $list = $this->service->getPaginatedList();
        
        $this->assertSame(array(), $list);
    }
    
    public function testGetListOneRestaurant()
    {
        $optionsList = array(array($this->restaurant, 'cuisson', array('Bleu', 'Saignant', 'A point', 'Bien cuit')));
        $expected = array();
        foreach ($optionsList as $option) {
            $expected[] = $this->createDishOption($option);
        }
        
        $list = $this->service->getPaginatedList();
        
        $this->assertSame($expected, $list);
    }
}
