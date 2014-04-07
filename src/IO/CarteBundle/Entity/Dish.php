<?php

namespace IO\CarteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dish
 *
 * @ORM\Table(name="dish")
 * @ORM\Entity()
 */
class Dish extends CarteItem
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
     * @ORM\Column(name="wp_id", type="integer", nullable=true)
     */
    private $wpId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=255, nullable=true)
     */
    private $tags;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Category", inversedBy="dishes")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $category;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $price;
    
    
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="IO\CarteBundle\Entity\DishOption")
     * @ORM\JoinTable(name="dish_dish_option",
     *      joinColumns={@ORM\JoinColumn(name="dish_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="option_id", referencedColumnName="id")}
     *      )
     */
    private $dishOptions;

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
     * Set category
     *
     * @param \IO\CarteBundle\Entity\Category $category
     * @return Dish
     */
    public function setCategory(\IO\CarteBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \IO\CarteBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dishOptions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add dishOptions
     *
     * @param \IO\CarteBundle\Entity\DishOption $dishOptions
     * @return Dish
     */
    public function addDishOption(\IO\CarteBundle\Entity\DishOption $dishOptions)
    {
        $this->dishOptions[] = $dishOptions;
    
        return $this;
    }

    /**
     * Remove dishOptions
     *
     * @param \IO\CarteBundle\Entity\DishOption $dishOptions
     */
    public function removeDishOption(\IO\CarteBundle\Entity\DishOption $dishOptions)
    {
        $this->dishOptions->removeElement($dishOptions);
    }

    /**
     * Get dishOptions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDishOptions()
    {
        return $this->dishOptions;
    }
}