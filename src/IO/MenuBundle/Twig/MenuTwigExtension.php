<?php

namespace IO\MenuBundle\Twig;

class MenuTwigExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'imageLink' => new \Twig_Filter_Method($this, 'imageLink'),
        );
    }

    /**
     * Get image link
     * 
     * @param String $imagePath
     * @return String
     */
    public function imageLink($imagePath)
    {
        if (substr($imagePath, 0, 5) == 'http') {
            return $imagePath;
        }

        return substr($imagePath, strpos($imagePath, 'web/') + 3);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'menu_twig_extension';
    }
}