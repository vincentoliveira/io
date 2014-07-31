<?php

namespace IO\ApiBundle\Utils;

use IO\UserBundle\Entity\User;
use IO\ApiBundle\Entity\UserToken;

/**
 * Api Visitor Interface
 * 
 * @author vincent
 */
interface ApiElementVisitorInterface
{
    public function visitUser(User $user);
    public function visitUserToken(UserToken $token);
//    public function visitCategory(CarteItem $category);
//    public function visitDish(CarteItem $dish);
//    public function visitOption(CarteItem $option);
//    public function visitOptionChoice(CarteItem $optionChoice);
//    public function visitMedia(Media $media);
}
