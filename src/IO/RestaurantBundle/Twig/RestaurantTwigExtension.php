<?php

namespace IO\RestaurantBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Tag;
use IO\RestaurantBundle\Enum\ItemTypeEnum;
use IO\RestaurantBundle\Entity\Media;

/**
 * Restaurant TwigExtension
 * 
 * @Service("io.restaurant_twig_extension")
 * @Tag("twig.extension")
 */
class RestaurantTwigExtension extends \Twig_Extension
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
            'restaurantCategories' => new \Twig_SimpleFunction('restaurantCategories', array($this, 'restaurantCategoriesFunction')),
        );
    }
    
    public function getFilters() {
        return array(
            'media' => new \Twig_SimpleFilter('media', array($this, 'mediaFilter')),
            'mediaWebPath' => new \Twig_SimpleFilter('mediaWebPath', array($this, 'mediaWebPathFilter')),
        );
    }
    
    /**
     * Return restaurant categories
     * 
     * @return array
     */
    public function restaurantCategoriesFunction() {
        $restaurant = $this->userSv->getUserRestaurant();
        $repositorty = $this->entityManger->getRepository('IORestaurantBundle:CarteItem');
        
        return $repositorty->getRestaurantMainCategory($restaurant->getId());
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
     * Return web path of media
     * 
     * @return array
     */
    public function mediaWebPathFilter($path)
    {
        return $this->mediaSv->getWebPathFromStr($path);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'restaurant_twig_extension';
    }
}