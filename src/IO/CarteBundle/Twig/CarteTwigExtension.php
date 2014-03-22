<?php

namespace IO\CarteBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

class CarteTwigExtension extends \Twig_Extension
{

    /*
     * @var Container
     */
    protected $container;

    /**
     * Constructor
     * 
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'imageLink' => new \Twig_SimpleFilter('imageLink', array($this, 'imageLinkFilter')),
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'restaurantCategories' => new \Twig_SimpleFunction('restaurantCategories', array($this, 'restaurantCategories')),
        );
    }

    /**
     * Get image link
     * 
     * @param String $imagePath
     * @return String
     */
    public function imageLinkFilter($imagePath)
    {
        if (substr($imagePath, 0, 4) == 'http') {
            return $imagePath;
        }
        
        $assets = $this->container->get('templating.helper.assets');
        $path = substr($imagePath, strpos($imagePath, 'web/') + 4);
        return $assets->getUrl($path);
    }
    
    
    /**
     * Return restaurant categories
     * 
     * @return array
     */
    public function restaurantCategories() {
        $userSv = $this->container->get('user.user_service');
        $categorySv = $this->container->get('menu.category');
        
        $restaurantName = $userSv->getUser()->getRestaurant()->getName();
        $categories = $categorySv->getRestaurantCategories($restaurantName);
        
        return $categories;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'carte_twig_extension';
    }
}