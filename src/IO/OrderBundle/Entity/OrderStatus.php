<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\Table(name="order_status")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderLineRepository")
 */
class OrderStatus
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
     * @ORM\ManyToOne(targetEntity="IO\OrderBundle\Entity\OrderData", inversedBy="orderStatuses")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $order;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="new_status", type="string", nullable=false)
     */
    private $newStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="old_status", type="string", nullable=false)
     */
    private $oldStatus;


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
     * @return OrderStatus
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
     * Set newStatus
     *
     * @param string $newStatus
     * @return OrderStatus
     */
    public function setNewStatus($newStatus)
    {
        $this->newStatus = $newStatus;
    
        return $this;
    }

    /**
     * Get newStatus
     *
     * @return string 
     */
    public function getNewStatus()
    {
        return $this->newStatus;
    }

    /**
     * Set oldStatus
     *
     * @param string $oldStatus
     * @return OrderStatus
     */
    public function setOldStatus($oldStatus)
    {
        $this->oldStatus = $oldStatus;
    
        return $this;
    }

    /**
     * Get oldStatus
     *
     * @return string 
     */
    public function getOldStatus()
    {
        return $this->oldStatus;
    }

    /**
     * Set order
     *
     * @param \IO\OrderBundle\Entity\OrderData $order
     * @return OrderStatus
     */
    public function setOrder(\IO\OrderBundle\Entity\OrderData $order = null)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return \IO\OrderBundle\Entity\OrderData 
     */
    public function getOrder()
    {
        return $this->order;
    }
}