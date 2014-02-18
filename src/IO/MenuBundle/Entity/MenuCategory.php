<?php

namespace IO\MenuBundle\Entity;

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
     * @param \IO\MenuBundle\Entity\Menu $menu
     * @return MenuCategory
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