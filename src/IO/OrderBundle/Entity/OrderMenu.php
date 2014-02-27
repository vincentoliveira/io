<?php

namespace IO\OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\Table(name="order_menu")
 * @ORM\Entity(repositoryClass="IO\OrderBundle\Repository\OrderMenuRepository")
 */
class OrderMenu
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
     * @ORM\ManyToOne(targetEntity="IO\CarteBundle\Entity\Menu")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     */
    private $menuId;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\OrderBundle\Entity\OrderLine", mappedBy="orderMenu", cascade={"remove", "persist"})
     */
    private $orderLines;

    /**
     * @var integer
     *
     * @ORM\Column(name="menu_price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $menuPrice;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderLines = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set menuPrice
     *
     * @param float $menuPrice
     * @return OrderMenu
     */
    public function setMenuPrice($menuPrice)
    {
        $this->menuPrice = $menuPrice;
    
        return $this;
    }

    /**
     * Get menuPrice
     *
     * @return float 
     */
    public function getMenuPrice()
    {
        return $this->menuPrice;
    }

    /**
     * Set menuId
     *
     * @param \IO\CarteBundle\Entity\Menu $menuId
     * @return OrderMenu
     */
    public function setMenuId(\IO\CarteBundle\Entity\Menu $menuId = null)
    {
        $this->menuId = $menuId;
    
        return $this;
    }

    /**
     * Get menuId
     *
     * @return \IO\CarteBundle\Entity\Menu 
     */
    public function getMenuId()
    {
        return $this->menuId;
    }

    /**
     * Add orderLines
     *
     * @param \IO\OrderBundle\Entity\OrderLine $orderLines
     * @return OrderMenu
     */
    public function addOrderLine(\IO\OrderBundle\Entity\OrderLine $orderLines)
    {
        $this->orderLines[] = $orderLines;
    
        return $this;
    }

    /**
     * Remove orderLines
     *
     * @param \IO\OrderBundle\Entity\OrderLine $orderLines
     */
    public function removeOrderLine(\IO\OrderBundle\Entity\OrderLine $orderLines)
    {
        $this->orderLines->removeElement($orderLines);
    }

    /**
     * Get orderLines
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrderLines()
    {
        return $this->orderLines;
    }
}