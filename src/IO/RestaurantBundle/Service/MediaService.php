<?php

namespace IO\RestaurantBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;

/**
 * Media Service
 * 
 * @Service("io.media_service")
 */
class MediaService
{
    const PICTURE_MAX_WIDTH = 600;

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
            if ($item->getMedia() !== null && !$item->getMedia()->getId()) {
                $item->setMedia(null);
            }
            
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
        $this->resizeImage($file->getPathName(), $dstPath, self::PICTURE_MAX_WIDTH);
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
    
    /**
     * Get web path of a media
     * 
     * @param \FJ\MainBundle\Entity\Media $media
     * @return string
     */
    public function getWebPath(Media $media)
    {
        return 'media/' . $media->getPath();
    }
    
    /**
     * Get web path of a media
     * 
     * @param string $path
     * @return string
     */
    public function getWebPathFromStr($path)
    {
        return 'media/' . $path;
    }
    
    protected function getUploadDir()
    {
        return $this->kernel->getRootDir() . '/../web/media/';
    }
    
    protected function fileIsValid(UploadedFile $file = null)
    {
        return $file !== null && $this->mimeTypeIsValid($file->getMimeType());
    }
    
    protected function mimeTypeIsValid($mimeType)
    {
        return in_array($mimeType, self::$acceptedMimeType);
    }
    
    /**
     * Permet de créer les vignettes à chaque upload d'image
     * 
     * @param string $srcImagePath  Chemin d'accès à l'image originale (sur le serveur)
     * @param string $dstImagePath  Chemin de destination de l'image redimensionné (sur le serveur)
     * @param string $maxWidth      Largeur de l'image voulue
     * @param string $maxHeight     Hauteur de l'image voulue (facultatif)
     * @param bool   $getSizes      If true, just return sizes, else do the job
     * 
     * @return boolean
     */
    protected function resizeImage($srcImagePath, $dstImagePath, $maxWidth = null, $maxHeight = null)
    {
        if (empty($srcImagePath) || !file_exists($srcImagePath) || is_dir($srcImagePath) || empty($maxWidth)) {
            return false;
        }

        list($srcImageWidth, $srcImageHeight, $source_image_type) = getimagesize($srcImagePath);
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $srcGdImage = imagecreatefromgif($srcImagePath);
                break;
            case IMAGETYPE_JPEG:
                $srcGdImage = imagecreatefromjpeg($srcImagePath);
                break;
            case IMAGETYPE_PNG:
                $srcGdImage = imagecreatefrompng($srcImagePath);
                break;
        }
        if (!isset($srcGdImage) || $srcGdImage === false) {
            return false;
        }

        $thumbnailImageWith = $maxWidth;
        if ($maxHeight != null) {
            $thumbnailImageHeight = $maxHeight;
        } else {
            $thumbnailImageHeight = (int) ($srcImageHeight * $maxWidth / $srcImageWidth );
        }

        if ($thumbnailImageWith >= $srcImageWidth || $thumbnailImageHeight >= $srcImageHeight) {
            copy($srcImagePath, $dstImagePath);
            return;
        }

        $thumbnailGdImage = imagecreatetruecolor($thumbnailImageWith, $thumbnailImageHeight);
        imagealphablending($thumbnailGdImage, false);
        imagesavealpha($thumbnailGdImage, true);
        imagecopyresampled($thumbnailGdImage, $srcGdImage, 0, 0, 0, 0, $thumbnailImageWith, $thumbnailImageHeight, $srcImageWidth, $srcImageHeight);

        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                imagegif($thumbnailGdImage, $dstImagePath);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($thumbnailGdImage, $dstImagePath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnailGdImage, $dstImagePath, 0);
                break;
        }
        imagedestroy($srcGdImage);
        imagedestroy($thumbnailGdImage);

        return true;
    }
}
