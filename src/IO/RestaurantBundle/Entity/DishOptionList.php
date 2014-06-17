<?php

namespace IO\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DishOption
 *
 * @ORM\Table(name="dish_option_list")
 * @ORM\Entity()
 */
class DishOptionList
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
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\CarteItem", inversedBy="options")
     * @ORM\JoinColumn(name="dish_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $dish;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\CarteItem")
     * @ORM\JoinColumn(name="option_list_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $optionList;

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
     * Set dish
     *
     * @param \IO\RestaurantBundle\Entity\CarteItem $dish
     * @return DishOptionList
     */
    public function setDish(\IO\RestaurantBundle\Entity\CarteItem $dish = null)
    {
        $this->dish = $dish;
    
        return $this;
    }

    /**
     * Get dish
     *
     * @return \IO\RestaurantBundle\Entity\CarteItem 
     */
    public function getDish()
    {
        return $this->dish;
    }

    /**
     * Set option list
     *
     * @param \IO\RestaurantBundle\Entity\CarteItem $optionList
     * @return DishOptionList
     */
    public function setOptionList(\IO\RestaurantBundle\Entity\CarteItem $optionList = null)
    {
        $this->optionList = $optionList;
    
        return $this;
    }

    /**
     * Get option list
     *
     * @return \IO\RestaurantBundle\Entity\CarteItem 
     */
    public function getOptionList()
    {
        return $this->optionList;
    }
}