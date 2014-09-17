<?php

namespace IO\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElementVisitorInterface;
use IO\ApiBundle\Utils\ApiElement;
use IO\UserBundle\Enum\CountryEnum;

/**
 * UserIdentity Entity
 *
 * @ORM\Table(name="user_identity")
 * @ORM\Entity
 */
class UserIdentity implements ApiElement
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
     * @ORM\Column(name="gender", type="integer", nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=127, nullable=false)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=127, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=127, nullable=false)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="date", nullable=false)
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=15, nullable=true)
     */
    private $nationality;
    
    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\Address")
     * @ORM\JoinColumn(name="address1_id", referencedColumnName="id", nullable=true)
     */
    private $address1;
    
    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\Address")
     * @ORM\JoinColumn(name="address2_id", referencedColumnName="id", nullable=true)
     */
    private $address2;
    
    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\Address")
     * @ORM\JoinColumn(name="address3_id", referencedColumnName="id", nullable=true)
     */
    private $address3;
    
    /**
     * @var PhoneNumber
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\PhoneNumber")
     * @ORM\JoinColumn(name="phone1_id", referencedColumnName="id", nullable=true)
     */
    private $phone1;
    
    /**
     * @var PhoneNumber
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\PhoneNumber")
     * @ORM\JoinColumn(name="phone2_id", referencedColumnName="id", nullable=true)
     */
    private $phone2;
    

    /**
     * {@inheritDoc}
     */
    public function accept(ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitUserIdentity($this);
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
     * Set gender
     *
     * @param integer $gender
     * @return UserIdentity
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return integer 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return UserIdentity
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return UserIdentity
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return UserIdentity
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     * @return UserIdentity
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    
        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime 
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set address1
     *
     * @param \IO\UserBundle\Entity\Address $address1
     * @return UserIdentity
     */
    public function setAddress1(\IO\UserBundle\Entity\Address $address1)
    {
        $this->address1 = $address1;
    
        return $this;
    }

    /**
     * Get address1
     *
     * @return \IO\UserBundle\Entity\Address 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param \IO\UserBundle\Entity\Address $address2
     * @return UserIdentity
     */
    public function setAddress2(\IO\UserBundle\Entity\Address $address2)
    {
        $this->address2 = $address2;
    
        return $this;
    }

    /**
     * Get address2
     *
     * @return \IO\UserBundle\Entity\Address 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set address3
     *
     * @param \IO\UserBundle\Entity\Address $address3
     * @return UserIdentity
     */
    public function setAddress3(\IO\UserBundle\Entity\Address $address3)
    {
        $this->address3 = $address3;
    
        return $this;
    }

    /**
     * Get address3
     *
     * @return \IO\UserBundle\Entity\Address 
     */
    public function getAddress3()
    {
        return $this->address3;
    }

    /**
     * Set phone1
     *
     * @param \IO\UserBundle\Entity\PhoneNumber $phone1
     * @return UserIdentity
     */
    public function setPhone1(\IO\UserBundle\Entity\PhoneNumber $phone1)
    {
        $this->phone1 = $phone1;
    
        return $this;
    }

    /**
     * Get phone1
     *
     * @return \IO\UserBundle\Entity\PhoneNumber 
     */
    public function getPhone1()
    {
        return $this->phone1;
    }

    /**
     * Set phone2
     *
     * @param \IO\UserBundle\Entity\PhoneNumber $phone2
     * @return UserIdentity
     */
    public function setPhone2(\IO\UserBundle\Entity\PhoneNumber $phone2)
    {
        $this->phone2 = $phone2;
    
        return $this;
    }

    /**
     * Get phone2
     *
     * @return \IO\UserBundle\Entity\PhoneNumber 
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     * @return UserIdentity
     */
    public function setNationality($nationality)
    {
        if (isset(CountryEnum::$countries[$nationality])) {
            $this->nationality = $nationality;
        }
    
        return $this;
    }

    /**
     * Get nationality
     *
     * @return string 
     */
    public function getNationality()
    {
        return $this->nationality;
    }
}