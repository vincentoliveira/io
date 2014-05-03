<?php

namespace IO\RestaurantBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;

/**
 * User Service
 * 
 * @Service("io.media_service")
 */
class MediaService
{

    protected static $acceptedMimeType = array(
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpg',
        'gif' => 'image/gif',
        'png' => 'image/png',
    );

    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * Kernel
     * 
     * @Inject("kernel")
     * @var \Symfony\Component\HttpKernel\Kernel
     */
    public $kernel;
    
    /**
     * Get loggued user
     *
     * @return \IO\UserBundle\Entity\User|null
     */
    public function handleMedia(CarteItem $item)
    {
        if ($item->getMedia() === null || !$this->fileIsValid($item->getMedia()->getFile())) {
            return null;
        }
        
        $media = $item->getMedia();
        $file = $media->getFile();
        
        // copy to upload dir with unique filename
        $uploadDir = $this->getUploadDir();
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $tmpPath = tempnam($uploadDir, '');
        $dstPath = $tmpPath . '.' . $file->guessExtension();
        copy($file->getPathName(), $dstPath);
        unlink($tmpPath);
        
        $media->setPath(basename($dstPath));
        
        $this->em->persist($media);
        $this->em->flush();

        return $media;
    }
    
    /**
     * Get absolute path of a media
     * 
     * @param \FJ\MainBundle\Entity\Media $media
     * @return string
     */
    public function getAbsolutePath(Media $media)
    {
        return $this->getUploadDir() . $media->getPath();
    }
    
    protected function getUploadDir()
    {
        return $this->kernel->getRootDir() . '/../web/media/';
    }
    
    protected function fileIsValid(UploadedFile $file = null)
    {
        return $file !== null && $this->mimeTypeIsValid($file->getMimeType());
    }
    
    protected function downloadFileIsValid($path)
    {
        return $this->mimeTypeIsValid(mime_content_type($path));
    }
    
    protected function mimeTypeIsValid($mimeType)
    {
        return in_array($mimeType, self::$acceptedMimeType);
    }
}
