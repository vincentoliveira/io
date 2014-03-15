<?php

namespace IO\OrderBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use IO\OrderBundle\Entity\Order;

class OrderTwigExtension extends \Twig_Extension
{
    /*
     * @var Container
     */

    protected $container;

    /**
     * Constructor
     * 
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'orderStatus' => new \Twig_Filter_Method($this, 'orderStatus'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'date_difference_minutes' => new \Twig_Function_Method($this, 'dateDifferenceMinutes'),
        );
    }

    /**
     * Get status name
     * 
     * @param String $imagePath
     * @return String
     */
    public function orderStatus($status)
    {
        if (!isset(Order::$typeLotAdmin[$status])) {
            return 'Erreur (' . $status . ')';
        }
        return ucfirst(Order::$typeLotAdmin[$status]);
    }

    
    /**
     * Calculate diffrence between two dates
     * 
     * @param \DateTime $date1
     * @param \DateTime $date2
     * @return 
     */
    public function dateDifferenceMinutes(\DateTime $date1, \DateTime $date2)
    {
        $diffInSec = $date1->getTimestamp() - $date2->getTimestamp();
        return intval($diffInSec / 60);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_twig_extension';
    }

}