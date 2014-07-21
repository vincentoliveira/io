<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use IO\OrderBundle\Enum\OrderStatusEnum;
use IO\OrderBundle\Enum\PaymentStatusEnum;

/**
 * Order
 *
 * @ORM\Table(name="order_data")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OrderData
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
     * @var integer
     *
     * @ORM\Column(name="ref", type="string", nullable=true)
     */
    private $ref;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
     */
    private $restaurant;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\OrderBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_date", type="datetime", nullable=false)
     */
    private $orderDate;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\OrderBundle\Entity\OrderLine", mappedBy="order", cascade={"remove", "persist"})
     */
    private $orderLines;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\OrderBundle\Entity\OrderStatus", mappedBy="order", cascade={"remove", "persist"})
     */
    private $orderStatuses;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\OrderBundle\Entity\OrderPayment", mappedBy="order", cascade={"remove", "persist"})
     */
    private $orderPayments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderLines = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderStatuses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderPayments = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get total price
     *
     * @return integer 
     */
    public function getTotalPrice()
    {
        $price = 0;
        foreach ($this->orderLines as $line) {
            $price += $line->getItemPrice();
        }
        return $price;
    }
    /**
     * Get no taxe total price
     *
     * @return integer 
     */
    public function getNoTaxeTotalPrice()
    {
        $price = 0;
        foreach ($this->orderLines as $line) {
            $price += $line->getItemPrice() / (1 + $line->getItemVat() / 100);
        }
        return $price;
    }
    
    /**
     * Get lasty status name
     * 
     * @return string
     */
    public function getLastStatus() {
        if ($this->orderStatuses->isEmpty()) {
            return OrderStatusEnum::STATUS_INIT;
        }
        
        return $this->orderStatuses->last()->getNewStatus();
    }
    
    /**
     * Get Payed Amount
     *
     * @return integer 
     */
    public function getPayedAmount()
    {
        $totalPayed = 0;
        foreach ($this->orderPayments as $payment) {
            if ($payment->getStatus() === PaymentStatusEnum::PAYMENT_SUCCESS) {
                $totalPayed += $payment->getAmount();
            }
        }
        return $totalPayed;
    }
    
    /**
     * Is order payed
     *
     * @return integer 
     */
    public function isPayed()
    {
        $totalPrice = $this->getTotalPrice();
        $totalPayed = $this->getPayedAmount();
        return $totalPayed >= $totalPrice;
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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return OrderData
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set orderDate
     *
     * @param \DateTime $orderDate
     * @return OrderData
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;
    
        return $this;
    }

    /**
     * Get orderDate
     *
     * @return \DateTime 
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * Set restaurant
     *
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return OrderData
     */
    public function setRestaurant(\IO\RestaurantBundle\Entity\Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    
        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \IO\RestaurantBundle\Entity\Restaurant 
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Add orderLines
     *
     * @param \IO\OrderBundle\Entity\OrderLine $orderLines
     * @return OrderData
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

    /**
     * Add orderStatuses
     *
     * @param \IO\OrderBundle\Entity\OrderStatus $orderStatuses
     * @return OrderData
     */
    public function addOrderStatus(\IO\OrderBundle\Entity\OrderStatus $orderStatus)
    {
        $this->orderStatuses[] = $orderStatus;
    
        return $this;
    }

    /**
     * Remove orderStatuses
     *
     * @param \IO\OrderBundle\Entity\OrderStatus $orderStatuses
     */
    public function removeOrderStatus(\IO\OrderBundle\Entity\OrderStatus $orderStatus)
    {
        $this->orderStatuses->removeElement($orderStatus);
    }

    /**
     * Get orderStatuses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderStatuses()
    {
        return $this->orderStatuses;
    }

    /**
     * Add orderPayments
     *
     * @param \IO\OrderBundle\Entity\OrderPayment $orderPayments
     * @return OrderData
     */
    public function addOrderPayment(\IO\OrderBundle\Entity\OrderPayment $orderPayments)
    {
        $this->orderPayments[] = $orderPayments;
    
        return $this;
    }

    /**
     * Remove orderPayments
     *
     * @param \IO\OrderBundle\Entity\OrderPayment $orderPayments
     */
    public function removeOrderPayment(\IO\OrderBundle\Entity\OrderPayment $orderPayments)
    {
        $this->orderPayments->removeElement($orderPayments);
    }

    /**
     * Get orderPayments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderPayments()
    {
        return $this->orderPayments;
    }

    /**
     * Set customer
     *
     * @param \IO\OrderBundle\Entity\Customer $customer
     * @return OrderData
     */
    public function setCustomer(\IO\OrderBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    
        return $this;
    }

    /**
     * Get customer
     *
     * @return \IO\OrderBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Add orderStatuses
     *
     * @param \IO\OrderBundle\Entity\OrderStatus $orderStatuses
     * @return OrderData
     */
    public function addOrderStatuse(\IO\OrderBundle\Entity\OrderStatus $orderStatuses)
    {
        $this->orderStatuses[] = $orderStatuses;
    
        return $this;
    }

    /**
     * Remove orderStatuses
     *
     * @param \IO\OrderBundle\Entity\OrderStatus $orderStatuses
     */
    public function removeOrderStatuse(\IO\OrderBundle\Entity\OrderStatus $orderStatuses)
    {
        $this->orderStatuses->removeElement($orderStatuses);
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return OrderData
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set ref
     *
     * @param string $ref
     * @return OrderData
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
    
        return $this;
    }

    /**
     * Get ref
     *
     * @return string 
     */
    public function getRef()
    {
        return $this->ref;
    }
}