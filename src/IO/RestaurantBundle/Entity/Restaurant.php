<?php

namespace IO\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElement;
use IO\ApiBundle\Utils\ApiElementVisitorInterface;

/**
 * Restaurant
 *
 * @ORM\Table(name="restaurant")
 * @ORM\Entity(repositoryClass="IO\RestaurantBundle\Repository\RestaurantRepository")
 */
class Restaurant implements ApiElement
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
     * @ORM\Column(name="name", type="string", length=63, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\RestaurantGroup", cascade={"persist"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $group;
    
    
    public function accept(ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitRestaurant($this);
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
     * Set name
     *
     * @param string $name
     * @return Restaurant
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
     * Set group
     *
     * @param \IO\RestaurantBundle\Entity\RestaurantGroup $group
     * @return Restaurant
     */
    public function setGroup(\IO\RestaurantBundle\Entity\RestaurantGroup $group = null)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get group
     *
     * @return \IO\RestaurantBundle\Entity\RestaurantGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }
}