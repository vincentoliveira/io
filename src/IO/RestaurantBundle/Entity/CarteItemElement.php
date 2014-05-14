<?php

namespace IO\RestaurantBundle\Entity;

use IO\RestaurantBundle\Service\Visitor\CarteItemVisitor;

/**
 * Carte Item Element Interface
 */
interface CarteItemElement
{
    public function accept(CarteItemVisitor $visitor);
}
