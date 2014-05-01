<?php

namespace IO\RestaurantBundle\Twig;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Tag;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

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
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'restaurantCategories' => new \Twig_SimpleFunction('restaurantCategories', array($this, 'restaurantCategoriesFunction')),
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'itemType' => new \Twig_SimpleFilter('itemType', array($this, 'itemTypeFilter')),
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
    
    public function itemTypeFilter($type)
    {
        if ($type === ItemTypeEnum::TYPE_CATEGORY) {
            return 'category';
        } elseif ($type === ItemTypeEnum::TYPE_DISH) {
            return 'dish';
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'carte_twig_extension';
    }
}