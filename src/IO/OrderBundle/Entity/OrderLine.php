<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\Table(name="order_line")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderLineRepository")
 */
class OrderLine
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
     * @ORM\ManyToOne(targetEntity="IO\OrderBundle\Entity\Order", inversedBy="orderLines")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Dish")
     * @ORM\JoinColumn(name="dish_id", referencedColumnName="id")
     */
    private $dish;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\OrderBundle\Entity\OrderMenu", inversedBy="orderLines")
     * @ORM\JoinColumn(name="menu_order_id", referencedColumnName="id")
     */
    private $orderMenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $itemPrice;


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
     * Set itemPrice
     *
     * @param float $itemPrice
     * @return OrderLine
     */
    public function setItemPrice($itemPrice)
    {
        $this->itemPrice = $itemPrice;
    
        return $this;
    }

    /**
     * Get itemPrice
     *
     * @return float 
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * Set order
     *
     * @param \IO\OrderBundle\Entity\Order $order
     * @return OrderLine
     */
    public function setOrder(\IO\OrderBundle\Entity\Order $order = null)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return \IO\OrderBundle\Entity\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set dish
     *
     * @param \IO\CarteBundle\Entity\Dish $dish
     * @return OrderLine
     */
    public function setDish(\IO\CarteBundle\Entity\Dish $dish = null)
    {
        $this->dish = $dish;
    
        return $this;
    }

    /**
     * Get dish
     *
     * @return \IO\CarteBundle\Entity\Dish 
     */
    public function getDish()
    {
        return $this->dish;
    }

    /**
     * Set orderMenu
     *
     * @param \IO\OrderBundle\Entity\OrderMenu $orderMenu
     * @return OrderLine
     */
    public function setOrderMenu(\IO\OrderBundle\Entity\OrderMenu $orderMenu = null)
    {
        $this->orderMenu = $orderMenu;
    
        return $this;
    }

    /**
     * Get orderMenu
     *
     * @return \IO\OrderBundle\Entity\OrderMenu 
     */
    public function getOrderMenu()
    {
        return $this->orderMenu;
    }
}