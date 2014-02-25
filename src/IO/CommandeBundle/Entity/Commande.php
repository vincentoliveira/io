<?php

namespace IO\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="IO\CommandeBundle\Repository\CommandeRepository")
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

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\CommandeBundle\Entity\CommandeLine", mappedBy="commande", cascade={"remove", "persist"})
     */
    private $commandeLines;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commandeLines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Commande
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set restaurant
     *
     * @param \IO\CarteBundle\Entity\Restaurant $restaurant
     * @return Commande
     */
    public function setRestaurant(\IO\CarteBundle\Entity\Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \IO\CarteBundle\Entity\Restaurant 
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Add commandeLines
     *
     * @param \IO\CommandeBundle\Entity\CommandeLine $commandeLines
     * @return Commande
     */
    public function addCommandeLine(\IO\CommandeBundle\Entity\CommandeLine $commandeLines)
    {
        $this->commandeLines[] = $commandeLines;

        return $this;
    }

    /**
     * Remove commandeLines
     *
     * @param \IO\CommandeBundle\Entity\CommandeLine $commandeLines
     */
    public function removeCommandeLine(\IO\CommandeBundle\Entity\CommandeLine $commandeLines)
    {
        $this->commandeLines->removeElement($commandeLines);
    }

    /**
     * Get commandeLines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommandeLines()
    {
        return $this->commandeLines;
    }

}