<?php

namespace IO\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Restaurant
 *
 * @ORM\Table(name="restaurant")
 * @ORM\Entity()
 */
class Restaurant
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
     * @ORM\OneToOne(targetEntity="IO\RestaurantBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="logo_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $logo;
    
    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="IO\RestaurantBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="background_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $background;
    
    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;


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
     * Set logo
     *
     * @param \IO\RestaurantBundle\Entity\Media $logo
     * @return Restaurant
     */
    public function setLogo(\IO\RestaurantBundle\Entity\Media $logo = null)
    {
        $this->logo = $logo;
    
        return $this;
    }

    /**
     * Get logo
     *
     * @return \IO\RestaurantBundle\Entity\Media 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set background
     *
     * @param \IO\RestaurantBundle\Entity\Media $background
     * @return Restaurant
     */
    public function setBackground(\IO\RestaurantBundle\Entity\Media $background = null)
    {
        $this->background = $background;
    
        return $this;
    }

    /**
     * Get background
     *
     * @return \IO\RestaurantBundle\Entity\Media 
     */
    public function getBackground()
    {
        return $this->background;
    }
}