<?php

namespace IO\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu choice
 *
 * @ORM\Table(name="menu_category")
 * @ORM\Entity()
 */
class MenuCategory
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
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\MenuBundle\Entity\Menu", inversedBy="menuChoices")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     */
    private $menu;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\MenuBundle\Entity\MenuChoice", mappedBy="menu", cascade={"remove", "persist"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $menuChoices;


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
     * Set price
     *
     * @param float $price
     * @return MenuChoice
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
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
     * Set menu
     *
     * @param \IO\MenuBundle\Entity\Menu $menu
     * @return MenuChoice
     */
    public function setMenu(\IO\MenuBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;
    
        return $this;
    }

    /**
     * Get menu
     *
     * @return \IO\MenuBundle\Entity\Menu 
     */
    public function getMenu()
    {
        return $this->menu;
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuChoices = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set order
     *
     * @param integer $order
     * @return MenuCategory
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
     * Add menuChoices
     *
     * @param \IO\MenuBundle\Entity\MenuChoice $menuChoices
     * @return MenuCategory
     */
    public function addMenuChoice(\IO\MenuBundle\Entity\MenuChoice $menuChoices)
    {
        $this->menuChoices[] = $menuChoices;
    
        return $this;
    }

    /**
     * Remove menuChoices
     *
     * @param \IO\MenuBundle\Entity\MenuChoice $menuChoices
     */
    public function removeMenuChoice(\IO\MenuBundle\Entity\MenuChoice $menuChoices)
    {
        $this->menuChoices->removeElement($menuChoices);
    }

    /**
     * Get menuChoices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenuChoices()
    {
        return $this->menuChoices;
    }
}