<?php

namespace IO\ApiBundle\Utils;

use IO\UserBundle\Entity\User;
use IO\ApiBundle\Entity\UserToken;

/**
 * Description of ApiVisitor
 *
 * @author vincent
 */
class ApiElementVisitor implements ApiElementVisitorInterface
{
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

    public function visitUserToken(UserToken $token)
    {
        return array(
            'user' => $token->getUser->accept($this),
            'token' => $token->getToken(),
            'expires_at' => $token->getExpiresAt(),
        );
    }

}
