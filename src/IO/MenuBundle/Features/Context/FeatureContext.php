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
class FeatureContext extends MinkContext
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
        $this->useContext('User', new UserContext($parameters));
        $this->useContext('Database', new DatabaseContext($parameters));
    }
    

    /**
     * Remplis un champ caché
     * 
     * @Given /^je remplis le champ caché "([^"]*)" avec "([^"]*)"$/
     * @param type $field
     * @param type $value
     */
    public function jeRemplisLeChampCacheAvec($field, $value)
    {
        $page = $this->getSession()->getPage();
        $node = $page->find('css', $field);
        if ($node === null) {
            $node = $page->find('css', 'input[name="' . $field . '"]');
        }

        $node->setValue($value);
    }
}
