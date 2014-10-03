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
 * Description of ApiVisitor
 *
 * @author vincent
 */
class ApiElementVisitor implements ApiElementVisitorInterface
{
    /**
     * {@inheritdoc}
     */
    public function visitUser(User $user)
    {
        if ($user->isEnabled() === false) {
            return array();
        }
        
        $identity = $wallet = null;
        if ($user->getIdentity()) {
            $identity = $user->getIdentity()->accept($this);
        }
        if ($user->getWallet()) {
            $wallet = $user->getWallet()->accept($this);
        }
        
        return array(
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'identity' => $identity,
            'wallet' => $wallet,
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitUserIdentity(UserIdentity $identity)
    {
        $address1 = $address2 = $address3 = $phone1 = $phone2 = null;
        
        if ($identity->getAddress1()) {
            $address1 = $identity->getAddress1()->accept($this);
        }
        if ($identity->getAddress2()) {
            $address2 = $identity->getAddress2()->accept($this);
        }
        if ($identity->getAddress3()) {
            $address3 = $identity->getAddress3()->accept($this);
        }
        if ($identity->getPhone1()) {
            $phone1 = $identity->getPhone1()->accept($this);
        }
        if ($identity->getPhone2()) {
            $phone2 = $identity->getPhone2()->accept($this);
        }
        
        return array(
            'gender' => $identity->getGender(),
            'firstname' => $identity->getFirstname(),
            'lastname' => $identity->getLastname(),
            'birthdate' => $identity->getBirthdate(),
            'email' => $identity->getEmail(),
            'nationality' => $identity->getNationality(),
            'address1' => $address1,
            'address2' => $address2,
            'address3' => $address3,
            'phone1' => $phone1,
            'phone2' => $phone2,
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitUserMangoWallet(UserWallet $wallet)
    {
        return array(
            'user_id' => $wallet->getUserId(),
            'wallet_id' => $wallet->getWalletId(),
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitAddress(Address $address)
    {
        return array(
            'name' => $address->getName(),
            'number' => $address->getNumber(),
            'street' => $address->getStreet(),
            'postcode' => $address->getPostCode(),
            'city' => $address->getCity(),
            'country' => $address->getCountry(),
            'building' => $address->getBuilding(),
            'staircase' => $address->getStaircase(),
            'floor' => $address->getFloor(),
            'digicode' => $address->getDigicode(),
            'intercom' => $address->getIntercom(),
            'comment' => $address->getComment(),
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitPhoneNumber(PhoneNumber $phone)
    {
        return array(
            'prefix' => $phone->getPrefix(),
            'number' => $phone->getNumber(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function visitAuthToken(AuthToken $token)
    {
        $userToken = array(
            'token' => $token->getToken(),
            'expires_at' => $token->getExpiresAt(),
            'user' => $token->getUser()->accept($this),
        );
        
        $restaurants = [];
        foreach ($token->getRestrictedRestaurants() as $restaurant) {
            $restaurants[] = $restaurant->accept($this);
        }
        if (!empty($restaurants)) {
            $userToken['restaurants'] = $restaurants;
        }
        
        return $userToken;
    }

    /**
     * {@inheritdoc}
     */
    public function visitRestaurant(Restaurant $restaurant)
    {
        return array(
            'id' => $restaurant->getId(),
            'name' => $restaurant->getName(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function visitCategory(CarteItem $category)
    {
        if ($category->isVisible() === false || $category->getChildren()->isEmpty()) {
            return null;
        }
        
        $result = array(
            'id' => $category->getId(),
            'type' => $category->getItemType(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'products' => array(),
        );
        
        if ($category->getMedia()) {
            $result['media'] = $category->getMedia()->accept($this);
        }
        
        foreach ($category->getChildren() as $child) {
            $item = $child->accept($this);
            if ($item !== null) {
                $result['products'][] = $item;
            }
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitDish(CarteItem $dish)
    {
        if ($dish->isVisible() === false) {
            return;
        }
        
        $vat = $dish->getVat()->getValue();
        if ($vat === null) {
            // default VAT
            $vat = 20.0;
        }
        
        $result = array(
            'id' => $dish->getId(),
            'name' => $dish->getName(),
            'shortName' => $dish->getShortName(),
            'description' => $dish->getDescription(),
            'type' => $dish->getItemType(),
            'price' => floatval($dish->getPrice()),
            'vat' => floatval($vat),
        );
        
        if ($dish->getMedia()) {
            $media = $dish->getMedia()->accept($this);
            if ($media !== null) {
                $result['media'] = $media;
            }
        }
        
        $result['options'] = array();
        foreach ($dish->getDishOptions() as $dishOption) {
            $result['options'][] = $dishOption->accept($this);
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitOption(CarteItem $option) {
        
        $choices = array();
        foreach ($option->getChildren() as $optionChoice) {
            $choices[] = $optionChoice->accept($this);
        }
        
        $result = array(
            'id' => $option->getId(),
            'name' => $option->getName(),
            'min_choice' => 1,
            'max_choice' => 1,
            'choices' => $choices,
        );
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitOptionChoice(CarteItem $optionChoice) {
        $result = array(
            'id' => $optionChoice->getId(),
            'name' => $optionChoice->getName(),
            'price' => floatval($optionChoice->getPrice()),
        );
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitMedia(Media $media)
    {
        $result = array(
            'id' => $media->getId(),
            'path' => $media->getPath(),
        );
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitOrderData(OrderData $orderData)
    {
        $result = array(
            'id' => $orderData->getId(),
            'delivery_date' => $orderData->getOrderDate(),
            'status' => $orderData->getLastStatus(),
            'products' => array(),
            'payments' => array(), 
            'total' => $orderData->getTotalPrice(),
            'no_tax_total' => $orderData->getNoTaxeTotalPrice(),
            'vat_amount' => $orderData->getVatAmount(),
            'total_unpayed' => $orderData->getTotalPrice() - $orderData->getPayedAmount(),
        );
        
        if ($orderData->getClient()) {
            $result['client'] = $orderData->getClient()->accept($this);
        }
        
        foreach ($orderData->getOrderLines() as $orderLine) {
            $result['products'][] = $orderLine->accept($this);
        }
        
        foreach ($orderData->getOrderPayments() as $payments) {
            $result['payments'][] = $payments->accept($this);
        }
        
        return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitOrderLine(OrderLine $orderLine)
    {
        return array(
            'product_id' => $orderLine->getItem() !== null ? $orderLine->getItem()->getId() : null,
            'name' => $orderLine->getItemName(),
            'short_name' => $orderLine->getItemShortName(),
            'extra' => $orderLine->getExtra(),
            'vat' => floatval($orderLine->getItemVat()),
            'price' => floatval($orderLine->getItemPrice()),
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitOrderPayment(OrderPayment $orderPayment)
    {
        return array(
            'id' => $orderPayment->getId(),
            'date' => $orderPayment->getDate(),
            'status' => $orderPayment->getStatus(),
            'amount' => float($orderPayment->getAmount()),
        );
    }
    
    public function visitCustomer(Customer $customer)
    {
        return array(
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'phone' => $customer->getPhone(),
        );
    }

}
