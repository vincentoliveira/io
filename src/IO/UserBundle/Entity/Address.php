<?php

namespace IO\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElementVisitorInterface;
use IO\ApiBundle\Utils\ApiElement;
use IO\UserBundle\Enum\CountryEnum;

/**
 * Address Entity
 *
 * @ORM\Table(name="address")
 * @ORM\Entity
 */
class Address implements ApiElement
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
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="string", length=15, nullable=false)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=127, nullable=false)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="post_code", type="string", length=5, nullable=false)
     */
    private $postCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=127, nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=15, nullable=false)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="building", type="string", length=15, nullable=true)
     */
    private $building;

    /**
     * @var string
     *
     * @ORM\Column(name="staircase", type="string", length=15, nullable=true)
     */
    private $staircase;
    
    /**
     * @var string
     *
     * @ORM\Column(name="floor", type="string", length=15, nullable=true)
     */
    private $floor;
    
    /**
     * @var string
     *
     * @ORM\Column(name="digicode", type="string", length=15, nullable=true)
     */
    private $digicode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="intercom", type="string", length=15, nullable=true)
     */
    private $intercom;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * {@inheritDoc}
     */
    public function accept(ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitAddress($this);
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
     * @return Address
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
     * Set number
     *
     * @param integer $number
     * @return Address
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;
    
        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set postCode
     *
     * @param string $postCode
     * @return Address
     */
    public function setPostCode($postCode)
    {
        $this->postCode = $postCode;
    
        return $this;
    }

    /**
     * Get postCode
     *
     * @return string 
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Address
     */
    public function setCountry($country)
    {
        if (isset(CountryEnum::$countries[$country])) {
            $this->country = $country;
        }
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set building
     *
     * @param string $building
     * @return Address
     */
    public function setBuilding($building)
    {
        $this->building = $building;
    
        return $this;
    }

    /**
     * Get building
     *
     * @return string 
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * Set staircase
     *
     * @param string $staircase
     * @return Address
     */
    public function setStaircase($staircase)
    {
        $this->staircase = $staircase;
    
        return $this;
    }

    /**
     * Get staircase
     *
     * @return string 
     */
    public function getStaircase()
    {
        return $this->staircase;
    }

    /**
     * Set floor
     *
     * @param string $floor
     * @return Address
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;
    
        return $this;
    }

    /**
     * Get floor
     *
     * @return string 
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * Set digicode
     *
     * @param string $digicode
     * @return Address
     */
    public function setDigicode($digicode)
    {
        $this->digicode = $digicode;
    
        return $this;
    }

    /**
     * Get digicode
     *
     * @return string 
     */
    public function getDigicode()
    {
        return $this->digicode;
    }

    /**
     * Set intercom
     *
     * @param string $intercom
     * @return Address
     */
    public function setIntercom($intercom)
    {
        $this->intercom = $intercom;
    
        return $this;
    }

    /**
     * Get intercom
     *
     * @return string 
     */
    public function getIntercom()
    {
        return $this->intercom;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Address
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }
}