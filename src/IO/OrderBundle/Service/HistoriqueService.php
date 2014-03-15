<?php

namespace IO\OrderBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use IO\CarteBundle\Entity\Restaurant;

/**
 * Historique Service
 */
class HistoriqueService
{
    
    /*
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
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Get $restaurant historique between $from and $to query builder
     * 
     * @param \IO\CarteBundle\Entity\Restaurant $restaurant
     * @param \DateTim $from
     * @param \DateTime $to
     * @param array $filters
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getHistoriqueQueryBuilder(Restaurant $restaurant, \DateTime $dateFrom, \DateTime $dateTo)
    {
        $orderRepo = $this->em->getRepository('IOOrderBundle:Order');
        $queryBuilder = $orderRepo->createQueryBuilder('_order')
                ->select('_order')
                ->andWhere('_order.restaurant = :restaurant')
                ->andWhere('_order.orderDate >= :dateFrom')
                ->andWhere('_order.orderDate <= :dateTo')
                ->setParameter(':restaurant', $restaurant)
                ->setParameter(':dateFrom', $dateFrom)
                ->setParameter(':dateTo', $dateTo);
        
        return $queryBuilder;
    }
    
    /**
     * Get $restaurant historique between $from and $to
     * 
     * @param \IO\CarteBundle\Entity\Restaurant $restaurant
     * @param \DateTim $from
     * @param \DateTime $to
     * @param array $filters
     * @return array
     */
    public function getHistorique(Restaurant $restaurant, \DateTime $dateFrom, \DateTime $dateTo)
    {
        $queryBuilder = $this->getHistoriqueQueryBuilder($restaurant, $dateFrom, $dateTo);
        return $queryBuilder->getQuery()->getResult();
    }
    
    
    /**
     * Get $restaurant historique between $from and $to
     * 
     * @param \IO\CarteBundle\Entity\Restaurant $restaurant
     * @param \DateTim $from
     * @param \DateTime $to
     * @param array $filters
     * @return array
     */
    public function getCsv(Restaurant $restaurant, \DateTime $dateFrom, \DateTime $dateTo)
    {
        $queryBuilder = $this->getHistoriqueQueryBuilder($restaurant, $dateFrom, $dateTo);
        $historique = $queryBuilder->getQuery()->getResult();
        
        $handle = tmpfile();
        
        $headers = array('Numero de commande', 'Date', 'Heure', 'Durée (en minutes)', 'Prix');
        foreach ($headers as $key => $datum) { $headers[$key] = utf8_decode($datum); } 
        fputcsv($handle, $headers, ';');
        
        foreach ($historique as $order) {
            $data = array(
                $order->getId(),
                $order->getOrderDate()->format('d/m/Y'),
                $order->getOrderDate()->format('h:i'),
                intval(($order->getUpdateDate()->getTimestamp() - $order->getOrderDate()->getTimestamp()) / 60),
                $order->getPrice(),
            );
            
            foreach ($data as $key => $datum) { $headers[$key] = utf8_decode($datum); } 
            fputcsv($handle, $data, ';');
        }
        
        
        fseek($handle, 0);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return $content;
    }
}
