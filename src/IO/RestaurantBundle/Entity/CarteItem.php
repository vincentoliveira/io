<?php

namespace IO\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarteItem
 *
 * @ORM\Table(name="carte_item")
 * @ORM\Entity(repositoryClass="IO\RestaurantBundle\Repository\CarteItemRepository")
 */
class CarteItem implements CarteItemElement
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
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\Restaurant")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
     */
    private $restaurant;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;
    
    /**
     * @var CarteItem
     *
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\CarteItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;
    
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="IO\RestaurantBundle\Entity\CarteItem", mappedBy="parent", cascade={"remove", "persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $children;
    
    /**
     * @var string
     *
     * @ORM\Column(name="item_type", type="string", length=15, nullable=false)
     */
    private $itemType;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=31, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=31, nullable=true)
     */
    private $shortName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\OneToOne(targetEntity="IO\RestaurantBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $media;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2, nullable=true)
     */
    private $price;
    
    /**
     * @var Taxe
     *
     * @ORM\ManyToOne(targetEntity="IO\RestaurantBundle\Entity\ValueAddedTax")
     * @ORM\JoinColumn(name="vat_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $vat;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;
    
    /**
     * @ORM\ManyToMany(targetEntity="CarteItem")
     * @ORM\JoinTable(name="dish_options",
     *      joinColumns={@ORM\JoinColumn(name="dish_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="option_id", referencedColumnName="id")}
     * )
     */
    private $dishOptions;

    /**
     * Accept Carte Item Visitor
     * 
     * @param \IO\RestaurantBundle\Service\Visitor\CarteItemVisitor $visitor
     */
    public function accept(\IO\RestaurantBundle\Service\Visitor\CarteItemVisitor $visitor)
    {
        if ($this->itemType === \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY) {
            return $visitor->visitCategory($this);
        } elseif ($this->itemType === \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_DISH) {
            return $visitor->visitDish($this);
        } elseif ($this->itemType === \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_OPTION) {
            return $visitor->visitOption($this);
        } elseif ($this->itemType === \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_OPTION_CHOICE) {
            return $visitor->visitOptionChoice($this);
        }
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dishOptions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set itemType
     *
     * @param string $itemType
     * @return CarteItem
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
     * Set visible
     *
     * @param boolean $visible
     * @return CarteItem
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Is visible
     *
     * @return boolean 
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return CarteItem
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
     * Set description
     *
     * @param string $description
     * @return CarteItem
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return CarteItem
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        if ($this->price === null) {
            return 0;
        }
        return $this->price;
    }

    /**
     * Set vat
     *
     * @param float $vat
     * @return CarteItem
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    
        return $this;
    }

    /**
     * Get vat
     *
     * @return float 
     */
    public function getVat()
    {
        if ($this->vat === null) {
            return 0;
        }
        return $this->vat;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return CarteItem
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set restaurant
     *
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return CarteItem
     */
    public function setRestaurant(\IO\RestaurantBundle\Entity\Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    
        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \IO\RestaurantBundle\Entity\Restaurant 
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Set parent
     *
     * @param \IO\RestaurantBundle\Entity\CarteItem $parent
     * @return CarteItem
     */
    public function setParent(\IO\RestaurantBundle\Entity\CarteItem $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \IO\RestaurantBundle\Entity\CarteItem 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \IO\RestaurantBundle\Entity\CarteItem $children
     * @return CarteItem
     */
    public function addChild(\IO\RestaurantBundle\Entity\CarteItem $child)
    {
        $this->children[] = $child;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \IO\RestaurantBundle\Entity\CarteItem $children
     */
    public function removeChild(\IO\RestaurantBundle\Entity\CarteItem $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set media
     *
     * @param \IO\RestaurantBundle\Entity\Media $media
     * @return CarteItem
     */
    public function setMedia(\IO\RestaurantBundle\Entity\Media $media = null)
    {
        $this->media = $media;
    
        return $this;
    }

    /**
     * Get media
     *
     * @return \IO\RestaurantBundle\Entity\Media 
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     * @return CarteItem
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    
        return $this;
    }

    /**
     * Get shortName
     *
     * @return string 
     */
    public function getShortName()
    {
        return empty($this->shortName) ? $this->name : $this->shortName;
    }

    /**
     * Add option
     *
     * @param CarteItem $option
     * @return CarteItem
     */
    public function addDishOption(CarteItem $option)
    {
        $this->dishOptions[] = $option;
    
        return $this;
    }

    /**
     * Remove option
     *
     * @param CarteItem $option
     */
    public function removeDishOption(CarteItem $option)
    {
        $this->dishOptions->removeElement($option);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDishOptions()
    {
        return $this->dishOptions;
    }
}