<?php

namespace IO\MenuBundle\Features\Context;

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Symfony2Extension\Context\KernelAwareInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Exception\ExpectationException;
use IO\MenuBundle\Features\Context\RunningContext;

/**
 * Require 3rd-party libraries here:
 */
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Features global context
 */
class FeatureGlobal extends MinkContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        ini_set('memory_limit', -1);

        $this->parameters = $parameters;
    }

    /**
     * @Given /^je suis connecté en tant que "([^"]*)"$/
     */
    public function jeSuisConnecteEnTantQue($login)
    {
        $session = $this->getSession();
        $session->visit($this->locatePath('login'));
        $page = $session->getPage();
        $page->fillField('username', $login);
        $page->fillField('password', 'test');
        $page->pressButton('_submit');
    }
}
