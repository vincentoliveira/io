<?php

namespace IO\RestaurantBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Restaurant;

/**
 * Carte Item Service
 * 
 * @Service("io.carte_item_service")
 */
class CarteItemService
{
    
    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;
    
    /**
     * Get carte items
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return array
     */
    public function getCarte(Restaurant $restaurant)
    {
        $criteria = array(
            'restaurant' => $restaurant,
            'parent' => null,
            'itemType' => \IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY,
        );
        $orderBy = array('position' => 'ASC');
        $categories = $this->em->getRepository('IORestaurantBundle:CarteItem')->findBy($criteria, $orderBy);
        
        $carte = array();
        $visitor = new Visitor\CarteGeneratorVisitor();
        foreach ($categories as $category) {
            $carte[] = $category->accept($visitor);
        }
        
        return $carte;
    }
}
