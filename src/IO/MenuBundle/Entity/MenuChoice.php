<?php

namespace IO\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu choice
 *
 * @ORM\Table(name="menu_choice")
 * @ORM\Entity()
 */
class MenuChoice
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
     * @ORM\Column(name="_order", type="integer", nullable=false)
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\MenuBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
     */
    private $restaurant;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="extra_price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $extraPrice;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\MenuBundle\Entity\Menu", inversedBy="menuCategory")
     * @ORM\JoinColumn(name="menu_category_id", referencedColumnName="id")
     */
    private $menuCategory;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\MenuBundle\Entity\Dish")
     * @ORM\JoinColumn(name="dish_id", referencedColumnName="id")
     */
    private $dish;

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
     * Set order
     *
     * @param integer $order
     * @return MenuChoice
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set extraPrice
     *
     * @param float $extraPrice
     * @return MenuChoice
     */
    public function setExtraPrice($extraPrice)
    {
        $this->extraPrice = $extraPrice;
    
        return $this;
    }

    /**
     * Get extraPrice
     *
     * @return float 
     */
    public function getExtraPrice()
    {
        return $this->extraPrice;
    }

    /**
     * Set restaurant
     *
     * @param \IO\MenuBundle\Entity\Restaurant $restaurant
     * @return MenuChoice
     */
    public function setRestaurant(\IO\MenuBundle\Entity\Restaurant $restaurant = null)
    {
        $this->restaurant = $restaurant;
    
        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \IO\MenuBundle\Entity\Restaurant 
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Set menuCategory
     *
     * @param \IO\MenuBundle\Entity\Menu $menuCategory
     * @return MenuChoice
     */
    public function setMenuCategory(\IO\MenuBundle\Entity\Menu $menuCategory = null)
    {
        $this->menuCategory = $menuCategory;
    
        return $this;
    }

    /**
     * Get menuCategory
     *
     * @return \IO\MenuBundle\Entity\Menu 
     */
    public function getMenuCategory()
    {
        return $this->menuCategory;
    }

    /**
     * Set dish
     *
     * @param \IO\MenuBundle\Entity\Dish $dish
     * @return MenuChoice
     */
    public function setDish(\IO\MenuBundle\Entity\Dish $dish = null)
    {
        $this->dish = $dish;
    
        return $this;
    }

    /**
     * Get dish
     *
     * @return \IO\MenuBundle\Entity\Dish 
     */
    public function getDish()
    {
        return $this->dish;
    }
}