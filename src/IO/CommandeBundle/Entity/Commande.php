<?php

namespace IO\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\MappedSuperclass
 */
class Commande
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
     */
    private $restaurant;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;
    
    
    
}