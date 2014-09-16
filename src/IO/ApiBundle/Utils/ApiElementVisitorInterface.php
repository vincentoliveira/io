<?php

namespace IO\ApiBundle\Utils;

use IO\UserBundle\Entity\User;
use IO\UserBundle\Entity\UserIdentity;
use IO\UserBundle\Entity\UserWallet;
use IO\UserBundle\Entity\Address;
use IO\UserBundle\Entity\PhoneNumber;
use IO\ApiBundle\Entity\AuthToken;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;
use IO\OrderBundle\Entity\OrderData;
use IO\OrderBundle\Entity\OrderLine;
use IO\OrderBundle\Entity\OrderPayment;
use IO\OrderBundle\Entity\Customer;

/**
 * Api Visitor Interface
 * 
 * @author vincent
 */
interface ApiElementVisitorInterface
{
    public function visitUser(User $user);
    public function visitUserIdentity(UserIdentity $identity);
    public function visitUserMangoWallet(UserWallet $identity);
    public function visitAddress(Address $address);
    public function visitPhoneNumber(PhoneNumber $phone);
    public function visitAuthToken(AuthToken $token);
    public function visitRestaurant(Restaurant $restaurant);
    public function visitCategory(CarteItem $category);
    public function visitDish(CarteItem $dish);
    public function visitOption(CarteItem $option);
    public function visitOptionChoice(CarteItem $optionChoice);
    public function visitMedia(Media $media);
    public function visitOrderData(OrderData $orderData);
    public function visitOrderLine(OrderLine $orderLine);
    public function visitOrderPayment(OrderPayment $orderPayment);
    public function visitCustomer(Customer $customer);
}
