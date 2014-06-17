<?php

namespace IO\RestaurantBundle\Service\Visitor;

use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;

/**
 * Carte Item Visitor Interface
 */
interface CarteItemVisitor
{
    public function visitCategory(CarteItem $category);
    public function visitDish(CarteItem $dish);
    public function visitOptionList(CarteItem $optionList);
    public function visitOption(CarteItem $option);
    public function visitMedia(Media $media);
}
