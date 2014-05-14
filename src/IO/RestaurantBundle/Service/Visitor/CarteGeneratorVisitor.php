<?php

namespace IO\RestaurantBundle\Service\Visitor;

use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;

/**
 * Carte Item Visitor Interface
 */
class CarteGeneratorVisitor implements CarteItemVisitor
{
    public function visitCategory(CarteItem $category)
    {
        if ($category->isVisible() === false) {
            return;
        }
        
        $result = array(
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'type' => $category->getItemType(),
            'children' => array(),
        );
        
        if ($category->getMedia()) {
            $result['media'] = $category->getMedia()->accept($this);
        }
        
        foreach ($category->getChildren() as $child) {
            $item = $child->accept($this);
            if ($item !== null) {
                $result['children'][] = $item;
            }
        }
        
        return $result;
    }
    
    public function visitDish(CarteItem $dish)
    {
        if ($dish->isVisible() === false) {
            return;
        }
        
        $vat = $dish->getVat();
        $parent = $dish->getParent();
        while (empty($vat) && $parent !== null) {
            $vat = $parent->getVat();
            $parent = $parent->getParent();
        }
        
        $result = array(
            'id' => $dish->getId(),
            'name' => $dish->getName(),
            'description' => $dish->getDescription(),
            'type' => $dish->getItemType(),
            'price' => $dish->getPrice(),
            'vat' => $vat,
        );
        
        if ($dish->getMedia()) {
            $media = $dish->getMedia()->accept($this);
            if ($media !== null) {
                $result['media'] = $media;
            }
        }
        
        return $result;
    }
    
    
    public function visitMedia(Media $media)
    {
        $result = array(
            'id' => $media->getId(),
            'path' => $media->getPath(),
        );
        
        return $result;
    }
}
