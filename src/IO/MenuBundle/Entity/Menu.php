<?php

namespace IO\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity()
 */
class Menu
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
     * @ORM\ManyToOne(targetEntity="IO\MenuBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")
     */
    private $restaurant;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\MenuBundle\Entity\Category", inversedBy="dishes")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=63, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=63, nullable=true)
     */
    private $description;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $price;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\MenuBundle\Entity\MenuChoice", mappedBy="menu", cascade={"remove", "persist"})
     */
    private $menuChoices;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuChoices = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Menu
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Menu
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
     * @return Menu
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
     * Set category
     *
     * @param \IO\MenuBundle\Entity\Category $category
     * @return Menu
     */
    public function setCategory(\IO\MenuBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \IO\MenuBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add menuChoices
     *
     * @param \IO\MenuBundle\Entity\MenuChoice $menuChoices
     * @return Menu
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