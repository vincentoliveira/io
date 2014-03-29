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

    /**
     * @var \IO\CarteBundle\Entity\Restaurant
     */
    protected $restaurant2;

    public function setUp()
    {
        parent::setUp();

        $this->service = $this->container->get('menu.dish_option');
        $this->truncate("IOCarteBundle:DishOption");
        $this->restaurant = $this->getRestaurantTest();
        $this->restaurant2 = $this->getRestaurantTest2();
    }


    /**
     * @dataProvider getListCases
     */
    public function testGetList($list1, $list2)
    {
        $expected = array();
        foreach ($list1 as $option) {
            $option[0] = $this->restaurant;
            $expected[] = $this->createDishOption($option);
        }
        foreach ($list2 as $option) {
            $option[0] = $this->restaurant2;
            $this->createDishOption($option);
        }

        $list = $this->service->getList($this->restaurant);

        $this->assertSame($expected, $list);
    }


    public  function getListCases()
    {
        return array(
            array(
                array(),
                array(),
            ),
            array(
                array(array(null, 'cuisson', array('Bleu', 'Saignant', 'A point', 'Bien cuit'))),
                array(),
            ),
            array(
                array(array(null, 'cuisson', array('Bleu', 'Saignant', 'A point', 'Bien cuit'))),
                array(array(null, 'sauce', array('Ketchup', 'Mayo'))),
            ),
        );
    }


}
