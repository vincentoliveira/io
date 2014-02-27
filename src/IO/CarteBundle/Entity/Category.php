<?php

namespace IO\CarteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="IO\CarteBundle\Repository\CategoryRepository")
 */
class Category extends CarteItem
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
     * @ORM\Column(name="wp_id", type="integer",  nullable=true)
     */
    private $wpId;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="tree_lvl", type="integer",  nullable=true)
     */
    private $treeLvl;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\CarteBundle\Entity\Category", mappedBy="parent", cascade={"remove", "persist"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $children;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\CarteBundle\Entity\Dish", mappedBy="category", cascade={"remove", "persist"})
     * @ORM\OrderBy({"order" = "ASC"})
     */
    private $dishes;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\CarteBundle\Entity\Menu", mappedBy="category", cascade={"remove", "persist"})
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
     * Set treeLvl
     *
     * @param integer $treeLvl
     * @return Category
     */
    public function setTreeLvl($treeLvl)
    {
        $this->treeLvl = $treeLvl;
    
        return $this;
    }

    /**
     * Get treeLvl
     *
     * @return integer 
     */
    public function getTreeLvl()
    {
        return $this->treeLvl;
    }

    /**
     * Set parent
     *
     * @param \IO\CarteBundle\Entity\Category $parent
     * @return Category
     */
    public function setParent(\IO\CarteBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \IO\CarteBundle\Entity\Category 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \IO\CarteBundle\Entity\Category $children
     * @return Category
     */
    public function addChildren(\IO\CarteBundle\Entity\Category $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \IO\CarteBundle\Entity\Category $children
     */
    public function removeChildren(\IO\CarteBundle\Entity\Category $children)
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
     * @param \IO\CarteBundle\Entity\Dish $dishes
     * @return Category
     */
    public function addDishe(\IO\CarteBundle\Entity\Dish $dishes)
    {
        $this->dishes[] = $dishes;
    
        return $this;
    }

    /**
     * Remove dishes
     *
     * @param \IO\CarteBundle\Entity\Dish $dishes
     */
    public function removeDishe(\IO\CarteBundle\Entity\Dish $dishes)
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
     * @param \IO\CarteBundle\Entity\Menu $menus
     * @return Category
     */
    public function addMenu(\IO\CarteBundle\Entity\Menu $menus)
    {
        $this->menus[] = $menus;
    
        return $this;
    }

    /**
     * Remove menus
     *
     * @param \IO\CarteBundle\Entity\Menu $menus
     */
    public function removeMenu(\IO\CarteBundle\Entity\Menu $menus)
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