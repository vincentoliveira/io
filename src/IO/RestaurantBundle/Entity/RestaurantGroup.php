<?php

namespace IO\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Restaurant Groupe
 *
 * @ORM\Table(name="restaurant_group")
 * @ORM\Entity()
 */
class RestaurantGroup
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
     * @ORM\OneToOne(targetEntity="IO\RestaurantBundle\Entity\VisualIdentity", cascade={"persist"})
     * @ORM\JoinColumn(name="visual_identity_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $visualIdentity;
    
    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\RestaurantBundle\Entity\Restaurant", mappedBy="group", cascade={"remove", "persist"})
     */
    private $restaurants;


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
     * Set website
     *
     * @param string $website
     * @return Restaurant
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }


    /**
     * Set visualIdentity
     *
     * @param \IO\RestaurantBundle\Entity\VisualIdentity $visualIdentity
     * @return RestaurantGroup
     */
    public function setVisualIdentity(\IO\RestaurantBundle\Entity\VisualIdentity $visualIdentity = null)
    {
        $this->visualIdentity = $visualIdentity;
    
        return $this;
    }

    /**
     * Get visualIdentity
     *
     * @return \IO\RestaurantBundle\Entity\VisualIdentity 
     */
    public function getVisualIdentity()
    {
        return $this->visualIdentity;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->restaurants = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add restaurants
     *
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurants
     * @return RestaurantGroup
     */
    public function addRestaurant(\IO\RestaurantBundle\Entity\Restaurant $restaurants)
    {
        $this->restaurants[] = $restaurants;
    
        return $this;
    }

    /**
     * Remove restaurants
     *
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurants
     */
    public function removeRestaurant(\IO\RestaurantBundle\Entity\Restaurant $restaurants)
    {
        $this->restaurants->removeElement($restaurants);
    }

    /**
     * Get restaurants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRestaurants()
    {
        return $this->restaurants;
    }
}