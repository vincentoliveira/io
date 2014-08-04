<?php

namespace IO\ApiBundle\Utils;

use IO\UserBundle\Entity\User;
use IO\ApiBundle\Entity\UserToken;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;

/**
 * Api Visitor Interface
 * 
 * @author vincent
 */
interface ApiElementVisitorInterface
{
    public function visitUser(User $user);
    public function visitUserToken(UserToken $token);
    public function visitRestaurant(Restaurant $restaurant);
    public function visitCategory(CarteItem $category);
    public function visitDish(CarteItem $dish);
    public function visitOption(CarteItem $option);
    public function visitOptionChoice(CarteItem $optionChoice);
    public function visitMedia(Media $media);
}
