<?php

namespace IO\DefaultBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Tag;
use IO\RestaurantBundle\Enum\ItemTypeEnum;
use IO\RestaurantBundle\Entity\Media;

/**
 * IO TwigExtension
 * 
 * @Service("io.twig_extension")
 * @Tag("twig.extension")
 */
class IOTwigExtension extends \Twig_Extension
{
    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;
    
    /**
     * Entity manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var EntityManager
     */
    public $entityManger;

    /**
     * User Service
     * 
     * @Inject("io.media_service")
     * @var \IO\RestaurantBundle\Service\MediaService
     */
    public $mediaSv;
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'date_difference' => new \Twig_SimpleFunction('date_difference', array($this, 'dateDifferenceFunction')),
        );
    }
    
    /**
     * Return restaurant categories
     * 
     * @return array
     */
    public function dateDifferenceFunction(\DateTime $date1, \DateTime $date2) {
        return $date1->diff($date2)->format('%im%ss');
    }
    
    
    /**
     * Return web path of media
     * 
     * @return array
     */
    public function mediaFilter(Media $media)
    {
        return $this->mediaSv->getWebPath($media);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'io_twig_extension';
    }
}