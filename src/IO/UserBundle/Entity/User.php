<?php

namespace IO\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use IO\ApiBundle\Utils\ApiElement;

/**
 * User entity (extends fosuser)
 * 
 * @ORM\Entity()
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements ApiElement
{
    /**
     * @var integer $id
     * @access protected
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var UserIdentity
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\UserIdentity")
     * @ORM\JoinColumn(name="identity_id", referencedColumnName="id", nullable=true)
     */
    private $identity;

    /**
     * @var \IO\RestaurantBundle\Entity\Restaurant
     *
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=true)
     */
    private $restaurant;
    
    /**
     * @var \IO\RestaurantBundle\Entity\RestaurantGroup
     *
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\RestaurantGroup")
     * @ORM\JoinColumn(name="restaurant_group_id", referencedColumnName="id", nullable=true)
     */
    private $restaurantGroup;
    
    /**
     * {@inheritDoc}
     */
    public function accept(\IO\ApiBundle\Utils\ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitUser($this);
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
     * Set restaurant
     *
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return User
     */
    public function setRestaurant(\IO\RestaurantBundle\Entity\Restaurant $restaurant = null)
    {
        $this->restaurant = $restaurant;
    
        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \IO\RestaurantBundle\Entity\Restaurant 
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Set restaurantGroup
     *
     * @param \IO\RestaurantBundle\Entity\RestaurantGroup $restaurantGroup
     * @return User
     */
    public function setRestaurantGroup(\IO\RestaurantBundle\Entity\RestaurantGroup $restaurantGroup = null)
    {
        $this->restaurantGroup = $restaurantGroup;
    
        return $this;
    }

    /**
     * Get restaurantGroup
     *
     * @return \IO\RestaurantBundle\Entity\RestaurantGroup 
     */
    public function getRestaurantGroup()
    {
        return $this->restaurantGroup;
    }

    /**
     * Set identity
     *
     * @param \IO\UserBundle\Entity\UserIdentity $identity
     * @return User
     */
    public function setIdentity(\IO\UserBundle\Entity\UserIdentity $identity = null)
    {
        $this->identity = $identity;
    
        return $this;
    }

    /**
     * Get identity
     *
     * @return \IO\UserBundle\Entity\UserIdentity 
     */
    public function getIdentity()
    {
        return $this->identity;
    }
}