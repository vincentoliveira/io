<?php

namespace IO\ApiBundle\Utils;

use IO\UserBundle\Entity\User;
use IO\UserBundle\Entity\UserIdentity;
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
        
        $identity = null;
        if ($user->getIdentity()) {
            $identity = $user->getIdentity()->accept($this);
        }
        
        return array(
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'identity' => $identity,
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function visitUserIdentity(UserIdentity $identity)
    {
        // TODO: show all data
        return array(
            'firstname' => $identity->getFirstname(),
            'lastname' => $identity->getLastname(),
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
            'price' => $dish->getPrice(),
            'vat' => $vat,
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
            'price' => $optionChoice->getPrice(),
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
            'delevery_date' => $orderData->getOrderDate(),
            'status' => $orderData->getLastStatus(),
            'customer' => null,
            'products' => array(),
            'payments' => array(),
            'total' => $orderData->getTotalPrice(),
            'total_unpayed' => $orderData->getTotalPrice() - $orderData->getPayedAmount(),
        );
        
        if ($orderData->getCustomer()) {
            $result['customer'] = $orderData->getCustomer()->accept($this);
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
            'vat' => $orderLine->getItemVat(),
            'price' => $orderLine->getItemPrice(),
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
            'amount' => $orderPayment->getAmount(),
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
