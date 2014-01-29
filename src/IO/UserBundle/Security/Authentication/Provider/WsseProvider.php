<?php

namespace IO\UserBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use IO\UserBundle\Security\Authentication\Token\WsseUserToken;

/**
 * Wsse Provider
 */
class WsseProvider implements AuthenticationProviderInterface
{

    /**
     * User provider
     * 
     * @var UserProviderInterface 
     */
    private $userProvider;

    /**
     * Cache directory
     * 
     * @var String 
     */
    private $cacheDir;

    /**
     * Constructor
     * 
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     * @param String $cacheDir
     */
    public function __construct(UserProviderInterface $userProvider, $cacheDir)
    {
        $this->userProvider = $userProvider;
        $this->cacheDir = $cacheDir;
    }

    /**
     * Authenticate
     * 
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @return \IO\UserBundle\Security\Authentication\Token\WsseUserToken
     * @throws AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUsername());

        if ($user && $this->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            $authenticatedToken = new WsseUserToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    /**
     * Validate digest.
     * Check :
     * - not from future
     * - not from past (timestamp > now() - 5 minutes)
     * - unique (in last 5 minutes)
     * - same digest
     * 
     * @param String $digest
     * @param String $nonce
     * @param String $created
     * @param String $secret
     * @return boolean
     */
    protected function validateDigest($digest, $nonce, $created, $secret)
    {
        // Check created time is not in the future
        if (strtotime($created) > time()) {
            return false;
        }

        // Expire timestamp after 5 minutes
        if (time() - strtotime($created) > 300) {
            return false;
        }

        // Validate nonce is unique within 5 minutes
        if (file_exists($this->cacheDir . '/' . $nonce) && file_get_contents($this->cacheDir . '/' . $nonce) + 300 > time()) {
            throw new NonceExpiredException('Previously used nonce detected');
        }

        // If cache directory does not exist we create it
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
        file_put_contents($this->cacheDir . '/' . $nonce, time());

        // Validate Secret
        $expected = base64_encode(sha1(base64_decode($nonce) . $created . $secret, true));
        
        return $digest === $expected;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof WsseUserToken;
    }

}
