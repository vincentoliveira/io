<?php

namespace IO\CarteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu choice
 *
 * @ORM\Table(name="menu_category")
 * @ORM\Entity()
 */
class MenuCategory extends CarteItem
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
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Menu", inversedBy="menuChoices")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $menu;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\CarteBundle\Entity\MenuChoice", mappedBy="menu", cascade={"remove", "persist"})
     * @ORM\OrderBy({"order" = "ASC"})
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
     * Set menu
     *
     * @param \IO\CarteBundle\Entity\Menu $menu
     * @return MenuCategory
     */
    public function setMenu(\IO\CarteBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;
    
        return $this;
    }

    /**
     * Get menu
     *
     * @return \IO\CarteBundle\Entity\Menu 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Add menuChoices
     *
     * @param \IO\CarteBundle\Entity\MenuChoice $menuChoices
     * @return MenuCategory
     */
    public function addMenuChoice(\IO\CarteBundle\Entity\MenuChoice $menuChoices)
    {
        $this->menuChoices[] = $menuChoices;
    
        return $this;
    }

    /**
     * Remove menuChoices
     *
     * @param \IO\CarteBundle\Entity\MenuChoice $menuChoices
     */
    public function removeMenuChoice(\IO\CarteBundle\Entity\MenuChoice $menuChoices)
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