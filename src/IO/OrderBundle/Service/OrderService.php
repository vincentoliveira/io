<?php

namespace IO\OrderBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use IO\OrderBundle\Entity\Order;


/**
 * Order Service
 */
class OrderService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param EntityManager
     */
    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

       
    /**
     * Get json array
     * 
     * @param \IO\CarteBundle\Service\Category $category
     * @param int $level
     * @return array
     */
    public function getJsonArray(Order $order = null)
    {
        if ($order === null) {
            return null;
        }
        return array(
            'id' => $order->getId(),
        );
    }

}
