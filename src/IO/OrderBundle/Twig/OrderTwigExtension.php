<?php

namespace IO\OrderBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Tag;
use IO\OrderBundle\Entity\OrderData;

/**
 * Restaurant TwigExtension
 * 
 * @Service("io.order_twig_extension")
 * @Tag("twig.extension")
 */
class OrderTwigExtension extends \Twig_Extension
{

    /**
     * User Service
     * 
     * @Inject("io.order_service")
     * @var \IO\OrderBundle\Service\OrderService
     */
    public $orderSv;
    
    public function getFilters() {
        return array(
            'receipt' => new \Twig_SimpleFilter('receipt', array($this, 'receiptFilter')),
        );
    }
    
    /**
     * Return order receipt
     * 
     * @return array
     */
    public function receiptFilter(OrderData $order) {
        return $this->orderSv->generateReceipt($order);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_twig_extension';
    }
}