<?php

namespace IO\MenuBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use IO\MenuBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Media Service
 */
class MediaService
{
    protected static $acceptedMimeType = array(
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpg',
        'gif' => 'image/gif',
        'png' => 'image/png',
    );

    const ICON_MAX_WIDTH = 32;
    const THUMB_MAX_WIDTH = 320;
    const MEDIA_DIR = '/../web/media/';
    const ICON_DIR = '/../web/media/icon/';
    const THUMBNAIL_DIR = '/../web/media/thumb/';

    /*
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var \Symfony\Component\Templating\Helper\CoreAssetsHelper
     */
    protected $assets;

    /**
     * Constructor
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Get json array
     * 
     * @param \IO\MenuBundle\Entity\Media $media
     * @return array
     */
    public function getJsonArray(Media $media = null)
    {
        if ($media === null) {
            return null;
        }
        
        if ($this->assets === null) {
            $this->assets = $this->container->get('templating.helper.assets');
        }
        
        return array(
            'id' => $media->getId(),
            'path' => $this->assets->getUrl($media->getPath()),
            'icon_path' => $this->assets->getUrl($media->getIconPath()),
            'thumbnail_path' => $this->assets->getUrl($media->getThumbnailPath()),
        );
    }
    
    /**
     * Is file valid ?
     * 
     * @param type $file
     * @return boolean
     */
    public function isFileValid($file)
    {
        return $file === null || ($file instanceof UploadedFile && in_array($file->getMimeType(), self::$acceptedMimeType));
    }

    /**
     * Create media from file
     * @param type $file
     * @throws \InvalidArgumentException
     * @throws InternalErrorException
     * @return Media
     */
    public function createMediaFromFile(UploadedFile $file)
    {
        if (!$this->isFileValid($file)) {
            throw new \InvalidArgumentException("Fichier non valide");
        }
        
        $ext = $file->guessExtension();
        $path = $this->generateUID($ext);
        if ($ext !== null) {
            $path = $path . '.' . $ext;
        }

        $paths = $this->copyMedia($file->getPathName(), $path);
        
        $media = new Media();
        $media->setPath($paths['upload']);
        $media->setIconPath($paths['icon']);
        $media->setThumbnailPath($paths['thumbnail']);
        
        $this->em->persist($media);
        $this->em->flush();

        return $media;
    }

    /**
     * Create media from url
     * 
     * @param String $url
     * @return Media
     */
    public function createMediaUrl($url)
    {
        $id = $this->generateUID();
        
        $media = new Media();
        $media->setPath($url);
        $media->setIconPath($url);
        $media->setThumbnailPath($url);

        $this->em->persist($media);
        $this->em->flush();

        return $media;
    }

    /**
     * Generate media unique ID
     * 
     * @param String $ext guessed extention
     * @return string UID
     */
    protected function generateUID($ext = null)
    {
        $ext = $ext !== null ? $ext : '';
        $rootDir = $this->getUploadDir();
        
        $id = $this->generateId(4);
        for ($i = 1; $i <= 12; $i++) {
            $path = $rootDir . $id . $ext;
            if (!file_exists($path)) {
                return $id;
            }
            $id = $id . $this->generateID(1);
        }

        return null;
    }

    /**
     * Generate media unique ID
     * @return string UID
     */
    protected function generateID($length = 4)
    {
        $key = '';
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

        for ($i = 0; $i < $length; $i++)
            $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];

        return $key;
    }

    /**
     * Copy $srcPath into media directory with $dstFilename as filename
     * Generate a thumbnail in thumbnail media directory
     * 
     * @param string $srcPath
     * @param string $dstFilename
     */
    protected function copyMedia($srcPath, $dstFilename)
    {
        $dstPath = $this->getUploadDir() . $dstFilename;
        $iconPath = $this->getIconUploadDir() . $dstFilename;
        $thumbnailPath = $this->getThumbnailUploadDir() . $dstFilename;

        // copy to upload dir
        copy($srcPath, $dstPath);

        // generate & icon thumbnail
        $this->resizeImage($dstPath, $iconPath, self::ICON_MAX_WIDTH);
        $this->resizeImage($dstPath, $thumbnailPath, self::THUMB_MAX_WIDTH);

        return array(
            'upload' => $dstPath,
            'icon' => $iconPath,
            'thumbnail' => $thumbnailPath,
        );
    }

    /**
     * Get upload directory path
     * 
     * @return string upload directory path
     */
    protected function getUploadDir()
    {
        $root = $this->container->get('kernel')->getRootDir();
        return $root . self::MEDIA_DIR;
    }

    /**
     * Get icon upload directory path
     * 
     * @return string icon upload directory path
     */
    protected function getIconUploadDir()
    {
        $root = $this->container->get('kernel')->getRootDir();
        return $root . self::ICON_DIR;
    }

    /**
     * Get thumbnail upload directory path
     * 
     * @return string thumbnail upload directory path
     */
    protected function getThumbnailUploadDir()
    {
        $root = $this->container->get('kernel')->getRootDir();
        return $root . self::THUMBNAIL_DIR;
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
