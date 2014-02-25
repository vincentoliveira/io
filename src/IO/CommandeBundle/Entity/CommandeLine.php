<?php

namespace IO\CommandeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\Table(name="commande_line")
 * @ORM\Entity(repositoryClass="IO\CommandeBundle\Repository\CommandeLineRepository")
 */
class CommandeLine
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
     * @ORM\ManyToOne(targetEntity="IO\CommandeBundle\Entity\Commande", inversedBy="commandeLines")
     * @ORM\JoinColumn(name="commande_id", referencedColumnName="id")
     */
    private $commande;

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
     * @return CommandeLine
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
     * @return CommandeLine
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
     * @return CommandeLine
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
     * Set commande
     *
     * @param \IO\CommandeBundle\Entity\Commande $commande
     * @return CommandeLine
     */
    public function setCommande(\IO\CommandeBundle\Entity\Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \IO\CommandeBundle\Entity\Commande 
     */
    public function getCommande()
    {
        return $this->commande;
    }

}