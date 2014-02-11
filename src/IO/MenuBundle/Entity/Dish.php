<?php

namespace IO\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dish
 *
 * @ORM\Table(name="dish")
 * @ORM\Entity()
 */
class Dish
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
     * @ORM\Column(name="wp_id", type="integer", nullable=true)
     */
    private $wpId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=63, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="string", length=255, nullable=true)
     */
    private $imageUrl;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=255, nullable=true)
     */
    private $tags;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\MenuBundle\Entity\Category", inversedBy="dishes")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $price;
    
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
     * @return Dish
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
     * @return Dish
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
     * @return Dish
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
     * @return Dish
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
     * Set imageUrl
     *
     * @param string $imageUrl
     * @return Dish
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    
        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string 
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return Dish
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    
        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set category
     *
     * @param \IO\MenuBundle\Entity\Category $category
     * @return Dish
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
     * Set price
     *
     * @param float $price
     * @return Dish
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
     * Set order
     *
     * @param integer $order
     * @return Dish
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
}