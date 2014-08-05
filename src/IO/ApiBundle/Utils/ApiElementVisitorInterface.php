<?php

namespace IO\ApiBundle\Utils;

use IO\UserBundle\Entity\User;
use IO\ApiBundle\Entity\AuthToken;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;
use IO\OrderBundle\Entity\OrderData;

/**
 * Api Visitor Interface
 * 
 * @author vincent
 */
interface ApiElementVisitorInterface
{
    public function visitUser(User $user);
    public function visitAuthToken(AuthToken $token);
    public function visitRestaurant(Restaurant $restaurant);
    public function visitCategory(CarteItem $category);
    public function visitDish(CarteItem $dish);
    public function visitOption(CarteItem $option);
    public function visitOptionChoice(CarteItem $optionChoice);
    public function visitMedia(Media $media);
    public function visitOrderData(OrderData $orderData);
}
