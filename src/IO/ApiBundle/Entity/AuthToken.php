<?php

namespace IO\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElement;

/**
 * AuthToken Entity
 *
 * @ORM\Table(name="auth_token")
 * @ORM\Entity(repositoryClass="IO\ApiBundle\Repository\AuthTokenRepository")
 */
class AuthToken implements ApiElement
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
     * @ORM\Column(name="plateform_name", type="string", length=15, nullable=true)
     */
    private $platformName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=31, nullable=false)
     */
    private $token;
    
    /**
     * @var string
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private $expiresAt;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;
    
    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="IO\RestaurantBundle\Entity\Restaurant")
     * @ORM\JoinTable(name="token_restricted_restaurant",
     *      joinColumns={@ORM\JoinColumn(name="token_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="restaurant_id", referencedColumnName="id")}
     * )     
     */
    private $restrictedRestaurants;


    public function accept(\IO\ApiBundle\Utils\ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitAuthToken($this);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->restrictedRestaurants = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set token
     *
     * @param string $token
     * @return AuthToken
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     * @return AuthToken
     */
    public function setExpireAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    
        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime 
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
    
    /**
     * Token has expired ?
     * 
     * @return boolean
     */
    public function hasExpired()
    {
        return $this->expiresAt !== null && $this->expiresAt < new \DateTime();
    }

    /**
     * Set user
     *
     * @param \IO\UserBundle\Entity\User $user
     * @return AuthToken
     */
    public function setUser(\IO\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \IO\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add restrictedRestaurants
     *
     * @param \IO\RestaurantBundle\Entity\Restaurant $restrictedRestaurants
     * @return AuthToken
     */
    public function addRestrictedRestaurant(\IO\RestaurantBundle\Entity\Restaurant $restrictedRestaurants)
    {
        $this->restrictedRestaurants[] = $restrictedRestaurants;
    
        return $this;
    }

    /**
     * Remove restrictedRestaurants
     *
     * @param \IO\RestaurantBundle\Entity\Restaurant $restrictedRestaurants
     */
    public function removeRestrictedRestaurant(\IO\RestaurantBundle\Entity\Restaurant $restrictedRestaurants)
    {
        $this->restrictedRestaurants->removeElement($restrictedRestaurants);
    }

    /**
     * Get restrictedRestaurants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRestrictedRestaurants()
    {
        return $this->restrictedRestaurants;
    }

    /**
     * Get restaurant
     *
     * @return \IO\RestaurantBundle\Entity\Restaurant|null
     */
    public function getRestaurant()
    {
        if ($this->restrictedRestaurants->count() === 1) {
            return $this->restrictedRestaurants->first();
        }
        return null;
    }

}