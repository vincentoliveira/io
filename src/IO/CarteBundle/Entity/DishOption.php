<?php

namespace IO\CarteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MenuOption
 *
 * @ORM\Table(name="dish_option")
 * @ORM\Entity(repositoryClass="IO\CarteBundle\Repository\DishOptionRepository")
 */
class DishOption
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
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
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
     * @ORM\Column(name="options", type="array", nullable=false)
     */
    private $options;


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
     * Set restaurant
     *
     * @param \IO\CarteBundle\Entity\Restaurant $restaurant
     * @return DishOption
     */
    public function setRestaurant(\IO\CarteBundle\Entity\Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    
        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \IO\CarteBundle\Entity\Restaurant 
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return DishOption
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
     * Set options
     *
     * @param array $options
     * @return DishOption
     */
    public function setOptions($options)
    {
        $this->options = $options;
    
        return $this;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOptions()
    {
        return $this->options;
    }
}