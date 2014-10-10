<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use IO\OrderBundle\Enum\OrderStatusEnum;
use IO\OrderBundle\Enum\PaymentStatusEnum;
use IO\ApiBundle\Utils\ApiElement;
use IO\ApiBundle\Utils\ApiElementVisitorInterface;

/**
 * Order
 *
 * @ORM\Table(name="order_data")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class OrderData implements ApiElement
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
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
     */
    private $restaurant;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\ApiBundle\Entity\AuthToken")
     * @ORM\JoinColumn(name="token_id", referencedColumnName="id")
     */
    private $token;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", nullable=true)
     */
    private $source;

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
     * @ORM\Column(name="order_date", type="datetime", nullable=true)
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
     * 
     * @param \IO\ApiBundle\Utils\ApiElementVisitorInterface $visitor
     */
    public function accept(ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitOrderData($this);
    }

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
     * @return float 
     */
    public function getTotalPrice()
    {
        $price = 0.0;
        foreach ($this->orderLines as $line) {
            $price += $line->getItemPrice();
        }
        return $price;
    }
    
    /**
     * Get no taxe total price
     *
     * @return float 
     */
    public function getNoTaxeTotalPrice()
    {
        $price = 0.0;
        foreach ($this->orderLines as $line) {
            $price += $line->getItemPrice() / (1 + $line->getItemVat() / 100);
        }
        return $price;
    }
    
    /**
     * Get vat amount
     *
     * @return integer 
     */
    public function getVatAmount()
    {
        $vatAmount = 0;
        foreach ($this->orderLines as $line) {
            $vatAmount += $line->getItemPrice() - ($line->getItemPrice() / (1 + $line->getItemVat() / 100));
        }
        return $vatAmount;
    }
    
    /**
     * Get last status name
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
     * @return float 
     */
    public function getPayedAmount()
    {
        $totalPayed = 0.0;
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
     * @return boolean 
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
     * Set source
     *
     * @param string $source
     * @return OrderData
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
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
     * Set token
     *
     * @param \IO\ApiBundle\Entity\AuthToken $token
     * @return OrderData
     */
    public function setToken(\IO\ApiBundle\Entity\AuthToken $token = null)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return \IO\ApiBundle\Entity\AuthToken 
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * Set client
     *
     * @param \IO\UserBundle\Entity\User $client
     * @return OrderData
     */
    public function setClient(\IO\UserBundle\Entity\User $client = null)
    {
        $this->client = $client;
    
        return $this;
    }

    /**
     * Get client
     *
     * @return \IO\UserBundle\Entity\User 
     */
    public function getClient()
    {
        return $this->client;
    }
}