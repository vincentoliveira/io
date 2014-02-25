<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\Table(name="order_line")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderLineRepository")
 */
class OrderLine
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
     * @ORM\ManyToOne(targetEntity="IO\OrderBundle\Entity\Order", inversedBy="orderLines")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(name="item_id", type="integer")
     */
    private $itemId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="item_type", type="string")
     */
    private $itemType;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $itemPrice;

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
     * Set itemId
     *
     * @param integer $itemId
     * @return OrderLine
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer 
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set itemType
     *
     * @param string $itemType
     * @return OrderLine
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * Get itemType
     *
     * @return string 
     */
    public function getItemType()
    {
        return $this->itemType;
    }

    /**
     * Set itemPrice
     *
     * @param float $itemPrice
     * @return OrderLine
     */
    public function setItemPrice($itemPrice)
    {
        $this->itemPrice = $itemPrice;

        return $this;
    }

    /**
     * Get itemPrice
     *
     * @return float 
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * Set order
     *
     * @param \IO\OrderBundle\Entity\Order $order
     * @return OrderLine
     */
    public function setOrder(\IO\OrderBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \IO\OrderBundle\Entity\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }

}