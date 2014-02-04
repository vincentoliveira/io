<?php

namespace IO\UserBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use IO\UserBundle\Security\Authentication\Token\WsseUserToken;

/**
 * Wsse Listener
 */
class WsseListener implements ListenerInterface
{
    /**
     *Security context
     * 
     * @var SecurityContextInterface 
     */
    protected $securityContext;
    
    /**
     * Authentication manager
     * 
     * @var AuthenticationManagerInterface 
     */
    protected $authenticationManager;

    /**
     * Constructor
     * 
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     * @param \Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * Handle WSSE authentification
     * 
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';
        if (!$request->headers->has('x-wsse') || 1 !== preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
            return;
        }

        $token = new WsseUserToken();
        $token->setUser($matches[1]);

        $token->digest   = $matches[2];
        $token->nonce    = $matches[3];
        $token->created  = $matches[4];

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);
            
            return;
        } catch (AuthenticationException $failed) {
            // ... you might log something here

        }
        
        // By default deny authorization with a '403 Forbidden' HTTP response
//        $response = new Response();
//        $response->setStatusCode(403);
//        $event->setResponse($response);
    }
}
