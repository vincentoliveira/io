<?php

namespace IO\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Restaurant Groupe
 *
 * @ORM\Table(name="visual_identity")
 * @ORM\Entity()
 */
class VisualIdentity
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
     * @ORM\OneToOne(targetEntity="IO\RestaurantBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="logo_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $logo;
    
    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="IO\RestaurantBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="background_img_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $backgroundImg;
    
    /**
     * @var string
     *
     * @ORM\Column(name="bg_color", type="string", length=7, nullable=true)
     */
    private $backgroundColor;
    
    /**
     * @var string
     *
     * @ORM\Column(name="text_color", type="string", length=7, nullable=true)
     */
    private $textColor;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title1_color", type="string", length=7, nullable=true)
     */
    private $title1Color;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title2_color", type="string", length=7, nullable=true)
     */
    private $title2Color;


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

    /**
     * Set backgroundColor
     *
     * @param string $backgroundColor
     * @return VisualIdentity
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    
        return $this;
    }

    /**
     * Get backgroundColor
     *
     * @return string 
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * Set textColor
     *
     * @param string $textColor
     * @return VisualIdentity
     */
    public function setTextColor($textColor)
    {
        $this->textColor = $textColor;
    
        return $this;
    }

    /**
     * Get textColor
     *
     * @return string 
     */
    public function getTextColor()
    {
        return $this->textColor;
    }

    /**
     * Set title1Color
     *
     * @param string $title1Color
     * @return VisualIdentity
     */
    public function setTitle1Color($title1Color)
    {
        $this->title1Color = $title1Color;
    
        return $this;
    }

    /**
     * Get title1Color
     *
     * @return string 
     */
    public function getTitle1Color()
    {
        return $this->title1Color;
    }

    /**
     * Set title2Color
     *
     * @param string $title2Color
     * @return VisualIdentity
     */
    public function setTitle2Color($title2Color)
    {
        $this->title2Color = $title2Color;
    
        return $this;
    }

    /**
     * Get title2Color
     *
     * @return string 
     */
    public function getTitle2Color()
    {
        return $this->title2Color;
    }

    /**
     * Set backgroundImg
     *
     * @param \IO\RestaurantBundle\Entity\Media $backgroundImg
     * @return VisualIdentity
     */
    public function setBackgroundImg(\IO\RestaurantBundle\Entity\Media $backgroundImg = null)
    {
        $this->backgroundImg = $backgroundImg;
    
        return $this;
    }

    /**
     * Get backgroundImg
     *
     * @return \IO\RestaurantBundle\Entity\Media 
     */
    public function getBackgroundImg()
    {
        return $this->backgroundImg;
    }
}