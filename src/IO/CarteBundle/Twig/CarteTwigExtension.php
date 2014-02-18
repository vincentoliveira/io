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
            'imageLink' => new \Twig_Filter_Method($this, 'imageLinkFilter'),
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
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menu_twig_extension';
    }
}