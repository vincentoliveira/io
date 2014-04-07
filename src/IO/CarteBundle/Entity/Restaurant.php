<?php

namespace IO\CarteBundle\Entity;

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
     * @ORM\Column(name="wp_base_url", type="string", length=255, nullable=true)
     */
    private $wpBaseUrl;


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
     * Set wpBaseUrl
     *
     * @param string $wpBaseUrl
     * @return Restaurant
     */
    public function setWpBaseUrl($wpBaseUrl)
    {
        $this->wpBaseUrl = $wpBaseUrl;
    
        return $this;
    }

    /**
     * Get wpBaseUrl
     *
     * @return string 
     */
    public function getWpBaseUrl()
    {
        return $this->wpBaseUrl;
    }
}