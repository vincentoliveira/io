<?php

namespace IO\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElement;
use IO\ApiBundle\Utils\ApiElementVisitorInterface;

/**
 * User MangoPay eWallet
 * 
 * @ORM\Table(name="user_wallet")
 * @ORM\Entity()
 */
class UserWallet implements ApiElement
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
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=63, nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="wallet_id", type="string", length=63, nullable=true)
     */
    private $walletId;
    
    /**
     * {@inheritDoc}
     */
    public function accept(ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitUserMangoWallet($this);
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
     * Set userId
     *
     * @param string $userId
     * @return UserWallet
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return string 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set walletId
     *
     * @param string $walletId
     * @return UserWallet
     */
    public function setWalletId($walletId)
    {
        $this->walletId = $walletId;
    
        return $this;
    }

    /**
     * Get walletId
     *
     * @return string 
     */
    public function getWalletId()
    {
        return $this->walletId;
    }
}