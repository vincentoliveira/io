<?php

namespace IO\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IO\ApiBundle\Utils\ApiElement;
use IO\ApiBundle\Utils\ApiElementVisitorInterface;

/**
 * Content
 *
 * @ORM\Table(name="media")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Media implements ApiElement
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
     * @var \DateTime
     *
     * @ORM\Column(name="upload_date", type="datetime", nullable=false)
     */
    private $uploadDate;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=127, nullable=false)
     */
    private $path;
    
    /**
     *
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile 
     */
    private $file;

    /**
     * Accept Api Element Visitor
     * 
     * @param ApiElementVisitorInterface $visitor
     */
    public function accept(ApiElementVisitorInterface $visitor)
    {
        return $visitor->visitMedia($this);
    }
    
    /**
     * Set file
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return \IO\RestaurantBundle\Entity\Media
     */
    public function setFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file = null)
    {
        $this->file = $file;
        return $this;
    }
    
    /**
     * Get file
     * 
     * @return type
     */
    public function getFile()
    {
        return $this->file;
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
     * Get uploadDate
     *
     * @return \DateTime 
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Media
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
    
    
    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setUploadDate(new \DateTime());
    }

    /**
     * Set uploadDate
     *
     * @param \DateTime $uploadDate
     * @return Media
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;
    
        return $this;
    }
}