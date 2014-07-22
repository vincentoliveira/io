<?php

namespace IO\RestaurantBundle\Tests\Service;

use IO\DefaultBundle\Tests\IOTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of MediaServiceTest
 */
class MediaServiceTest extends IOTestCase
{

    /**
     * @var \IO\RestaurantBundle\Service\MediaService
     */
    protected $service;

    public function setup()
    {
        parent::setup();
        $this->service = $this->container->get('io.media_service');
    }

    public function testHandleNoMedia()
    {
        $item = new \IO\RestaurantBundle\Entity\CarteItem();
        $result = $this->service->handleItemMedia($item);
        $this->assertNull($result);
    }

    public function testHandleEmpty()
    {
        $item = $this->generateItem(null);
        $result = $this->service->handleItemMedia($item);
        $this->assertNull($result);
    }

    public function testImportFromFileBadExtension()
    {
        $item = $this->generateItem(__DIR__ . '/../Fixtures/test_empty');
        $result = $this->service->handleItemMedia($item);
        $this->assertNull($result);
    }

    public function testImportFromFile()
    {
        $testFilePath = __DIR__ . '/../Fixtures/test.gif';
        $item = $this->generateItem($testFilePath);
        $result = $this->service->handleItemMedia($item);

        $this->assertInstanceOf('\IO\RestaurantBundle\Entity\Media', $result);
        $this->assertFileEquals($testFilePath, $this->service->getAbsolutePath($result));
    }
    
    protected function generateItem($path)
    {
        $media = new \IO\RestaurantBundle\Entity\Media();
        
        if ($path !== null) {
            $file = new UploadedFile($path, 'original.gif', null, filesize($path), UPLOAD_ERR_OK);
            $media->setFile($file);
        }
        
        $item = new \IO\RestaurantBundle\Entity\CarteItem();
        $item->setMedia($media);
        return $item;
    }
}