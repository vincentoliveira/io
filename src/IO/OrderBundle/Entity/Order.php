<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\Table(name="order_item")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Order
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
     * @ORM\OneToMany(targetEntity="IO\OrderBundle\Entity\OrderLine", mappedBy="order", cascade={"remove", "persist"})
     */
    private $orderLines;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderLines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Order
     * 
     * @ORM\PrePersist
     */
    public function setDate()
    {
        if ($this->date === null) {
            $this->date = new \DateTime();
        }
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
     * @return Order
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
     * Add orderLines
     *
     * @param \IO\OrderBundle\Entity\OrderLine $orderLines
     * @return Order
     */
    public function addOrderLine(\IO\OrderBundle\Entity\OrderLine $orderLines)
    {
        $this->orderLines[] = $orderLines;

        return $this;
    }

    /**
     * Remove orderLines
     *
     * @param \IO\OrderBundle\Entity\OrderLine $orderLines
     */
    public function removeOrderLine(\IO\OrderBundle\Entity\OrderLine $orderLines)
    {
        $this->orderLines->removeElement($orderLines);
    }

    /**
     * Get orderLines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderLines()
    {
        return $this->orderLines;
    }

}