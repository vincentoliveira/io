<?php

namespace IO\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElementVisitorInterface;
use IO\ApiBundle\Utils\ApiElement;

/**
 * PhoneNumber Entity
 *
 * @ORM\Table(name="phone_number")
 * @ORM\Entity
 */
class PhoneNumber implements ApiElement
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
     * @ORM\Column(name="prefix", type="string", length=7, nullable=true)
     */
    private $prefix;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=15, nullable=false)
     */
    private $number;

    /**
     * {@inheritDoc}
     */
    public function accept(ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitPhoneNumber($this);
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
     * Set prefix
     *
     * @param string $prefix
     * @return PhoneNumber
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    
        return $this;
    }

    /**
     * Get prefix
     *
     * @return string 
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return PhoneNumber
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }
}