<?php

namespace IO\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElement;

/**
 * UserToken Entity
 *
 * @ORM\Table(name="user_token")
 * @ORM\Entity(repositoryClass="IO\ApiBundle\Repository\UserTokenRepository")
 */
class UserToken implements ApiElement
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
     * @ORM\Column(name="name", type="string", length=15, nullable=false)
     */
    private $token;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=false)
     */
    private $expiresAt;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
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
        return $visitor->visitUserToken($this);
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
     * @return UserToken
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
     * @return UserToken
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
     * Set user
     *
     * @param \IO\UserBundle\Entity\User $user
     * @return UserToken
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
     * @return UserToken
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

}