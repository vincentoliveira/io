<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 *
 * @ORM\Table(name="order_item")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Order
{
    const WAITING = 1;
    const STARTER = 2;
    const STARTER_SERVED = 3;
    const DISH = 4;
    const DISH_SERVED = 5;
    const DESSERT = 6;
    const DESSERT_SERVED = 7;
    const PAID = 8;
    
    public static $typeLotAdmin = array(
        self::WAITING => "En attente",
        self::STARTER => "Préparation des entrées",
        self::STARTER_SERVED => "Entrées servis",
        self::DISH => "Préparation des plats",
        self::DISH_SERVED => "Plat servis",
        self::DESSERT => "Préparation des desserts",
        self::DESSERT_SERVED => "Dessert servis",
        self::PAID => "Payé",
    );
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
     */
    private $restaurant;

    /**
     * @var string
     *
     * @ORM\Column(name="table_name", type="string", nullable=true)
     */
    private $tableName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_date", type="datetime", nullable=false)
     */
    private $orderDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

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


    /**
     * Set tableName
     *
     * @param string $tableName
     * @return Order
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    
        return $this;
    }

    /**
     * Get tableName
     *
     * @return string 
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set orderDate
     *
     * @param \DateTime $orderDate
     * @return Order
     * @ORM\PrePersist
     */
    public function setOrderDate()
    {
        if ($this->orderDate === null) {
            $this->orderDate = new \DateTime();
        }
    
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
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Order
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdateDate()
    {
        $this->updateDate = new \DateTime();
    
        return $this;
    }
    
    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
    
    /**
     * Get total price
     *
     * @return integer 
     */
    public function getPrice()
    {
        $price = 0;
        foreach ($this->getOrderLines() as $orderLine) {
            $price += $orderLine->getItemPrice();
        }
        
        return $price;
    }
}