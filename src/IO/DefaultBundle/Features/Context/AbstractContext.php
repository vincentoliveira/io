<?php

namespace IO\DefaultBundle\Features\Context;

use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\Behat\Context\BehatContext;
use Symfony\Component\HttpKernel\KernelInterface;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * AbstractContext
 */
abstract class AbstractContext extends BehatContext implements KernelAwareInterface
{
    /**
     *
     * @var \Symfony\Component\HttpKernel\Kernel 
     */
    protected $kernel;
    protected $parameters;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->kernel->getContainer()->get('doctrine')->getManager();
    }
}
