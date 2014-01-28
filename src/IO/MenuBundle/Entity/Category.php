<?php

namespace IO\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity()
 */
class Category
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
     * @ORM\Column(name="wp_id", type="integer",  nullable=false)
     */
    private $wpId;

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
     * @ORM\Column(name="name", type="string", length=63, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\MenuBundle\Entity\Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\MenuBundle\Entity\Category", mappedBy="parent", cascade={"remove", "persist"})
     */
    private $children;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\MenuBundle\Entity\Dish", mappedBy="category", cascade={"remove", "persist"})
     */
    private $dishes;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\MenuBundle\Entity\Menu", mappedBy="category", cascade={"remove", "persist"})
     */
    private $menus;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dishes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->menus = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set wpId
     *
     * @param integer $wpId
     * @return Category
     */
    public function setWpId($wpId)
    {
        $this->wpId = $wpId;
    
        return $this;
    }

    /**
     * Get wpId
     *
     * @return integer 
     */
    public function getWpId()
    {
        return $this->wpId;
    }

    /**
     * Set restaurant
     *
     * @param \IO\MenuBundle\Entity\Restaurant $restaurant
     * @return Category
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
     * Set name
     *
     * @param string $name
     * @return Category
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
     * Set parent
     *
     * @param \IO\MenuBundle\Entity\Category $parent
     * @return Category
     */
    public function setParent(\IO\MenuBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \IO\MenuBundle\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \IO\MenuBundle\Entity\Category $children
     * @return Category
     */
    public function addChildren(\IO\MenuBundle\Entity\Category $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \IO\MenuBundle\Entity\Category $children
     */
    public function removeChildren(\IO\MenuBundle\Entity\Category $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add dishes
     *
     * @param \IO\MenuBundle\Entity\Dish $dishes
     * @return Category
     */
    public function addDishe(\IO\MenuBundle\Entity\Dish $dishes)
    {
        $this->dishes[] = $dishes;
    
        return $this;
    }

    /**
     * Remove dishes
     *
     * @param \IO\MenuBundle\Entity\Dish $dishes
     */
    public function removeDishe(\IO\MenuBundle\Entity\Dish $dishes)
    {
        $this->dishes->removeElement($dishes);
    }

    /**
     * Get dishes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDishes()
    {
        return $this->dishes;
    }

    /**
     * Add menus
     *
     * @param \IO\MenuBundle\Entity\Menu $menus
     * @return Category
     */
    public function addMenu(\IO\MenuBundle\Entity\Menu $menus)
    {
        $this->menus[] = $menus;
    
        return $this;
    }

    /**
     * Remove menus
     *
     * @param \IO\MenuBundle\Entity\Menu $menus
     */
    public function removeMenu(\IO\MenuBundle\Entity\Menu $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenus()
    {
        return $this->menus;
    }
}