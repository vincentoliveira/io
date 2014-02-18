<?php

namespace IO\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity()
 */
class Menu extends CarteItem
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\MenuBundle\Entity\MenuCategory", mappedBy="menu", cascade={"remove", "persist"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $menuCategories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menuCategories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add menuCategories
     *
     * @param \IO\MenuBundle\Entity\MenuCategory $menuCategories
     * @return Menu
     */
    public function addMenuCategorie(\IO\MenuBundle\Entity\MenuCategory $menuCategories)
    {
        $this->menuCategories[] = $menuCategories;
    
        return $this;
    }

    /**
     * Remove menuCategories
     *
     * @param \IO\MenuBundle\Entity\MenuCategory $menuCategories
     */
    public function removeMenuCategorie(\IO\MenuBundle\Entity\MenuCategory $menuCategories)
    {
        $this->menuCategories->removeElement($menuCategories);
    }

    /**
     * Get menuCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenuCategories()
    {
        return $this->menuCategories;
    }
}