<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElement;
use IO\ApiBundle\Utils\ApiElementVisitorInterface;

/**
 * CarteItem
 *
 * @ORM\Table(name="order_line")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderLineRepository")
 */
class OrderLine implements ApiElement
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
     * @ORM\ManyToOne(targetEntity="IO\OrderBundle\Entity\OrderData", inversedBy="orderLines")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\CarteItem")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $item;

    /**
     * @var string
     *
     * @ORM\Column(name="item_name", type="string", nullable=true)
     */
    private $itemName;

    /**
     * @var string
     *
     * @ORM\Column(name="item_short_name", type="string", nullable=true)
     */
    private $itemShortName;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $itemPrice;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_vat", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $itemVat;

    /**
     * @var string
     *
     * @ORM\Column(name="extra", type="string", nullable=true)
     */
    private $extra;

    /**
     * 
     * @param \IO\ApiBundle\Utils\ApiElementVisitorInterface $visitor
     */
    public function accept(ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitOrderLine($this);
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
     * Set itemName
     *
     * @param string $itemName
     * @return OrderLine
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    
        return $this;
    }

    /**
     * Get itemName
     *
     * @return string 
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * Set itemShortName
     *
     * @param string $itemShortName
     * @return OrderLine
     */
    public function setItemShortName($itemShortName)
    {
        $this->itemShortName = $itemShortName;
    
        return $this;
    }

    /**
     * Get itemShortName
     *
     * @return string 
     */
    public function getItemShortName()
    {
        return $this->itemShortName;
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
     * Set itemVat
     *
     * @param float $itemVat
     * @return OrderLine
     */
    public function setItemVat($itemVat)
    {
        $this->itemVat = $itemVat;
    
        return $this;
    }

    /**
     * Get itemVat
     *
     * @return float 
     */
    public function getItemVat()
    {
        return $this->itemVat;
    }

    /**
     * Set order
     *
     * @param \IO\OrderBundle\Entity\OrderData $order
     * @return OrderLine
     */
    public function setOrder(\IO\OrderBundle\Entity\OrderData $order = null)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return \IO\OrderBundle\Entity\OrderData 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set item
     *
     * @param \IO\RestaurantBundle\Entity\CarteItem $item
     * @return OrderLine
     */
    public function setItem(\IO\RestaurantBundle\Entity\CarteItem $item = null)
    {
        $this->item = $item;
    
        return $this;
    }

    /**
     * Get item
     *
     * @return \IO\RestaurantBundle\Entity\CarteItem 
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set extra
     *
     * @param string $extra
     * @return OrderLine
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    
        return $this;
    }

    /**
     * Get extra
     *
     * @return string 
     */
    public function getExtra()
    {
        return $this->extra;
    }
}