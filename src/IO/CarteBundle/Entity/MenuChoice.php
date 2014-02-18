<?php

namespace IO\CarteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu choice (table de jointure)
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
     * @ORM\Column(name="extra_price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $extraPrice;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Menu", inversedBy="menuCategory")
     * @ORM\JoinColumn(name="menu_category_id", referencedColumnName="id")
     */
    private $menuCategory;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Dish")
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
     * Set menuCategory
     *
     * @param \IO\CarteBundle\Entity\Menu $menuCategory
     * @return MenuChoice
     */
    public function setMenuCategory(\IO\CarteBundle\Entity\Menu $menuCategory = null)
    {
        $this->menuCategory = $menuCategory;
    
        return $this;
    }

    /**
     * Get menuCategory
     *
     * @return \IO\CarteBundle\Entity\Menu 
     */
    public function getMenuCategory()
    {
        return $this->menuCategory;
    }

    /**
     * Set dish
     *
     * @param \IO\CarteBundle\Entity\Dish $dish
     * @return MenuChoice
     */
    public function setDish(\IO\CarteBundle\Entity\Dish $dish = null)
    {
        $this->dish = $dish;
    
        return $this;
    }

    /**
     * Get dish
     *
     * @return \IO\CarteBundle\Entity\Dish 
     */
    public function getDish()
    {
        return $this->dish;
    }
}