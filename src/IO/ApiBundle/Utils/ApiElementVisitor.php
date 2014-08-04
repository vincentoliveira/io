<?php

namespace IO\ApiBundle\Utils;

use IO\UserBundle\Entity\User;
use IO\ApiBundle\Entity\UserToken;
use IO\RestaurantBundle\Entity\Restaurant;

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
        
        return array(
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function visitUserToken(UserToken $token)
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

}
