<?php

namespace IO\DefaultBundle\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Require 3rd-party libraries here:
 */
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Features global context
 */
class FeatureContext extends MinkContext implements KernelAwareInterface
{

    /**
     *
     * @var \Symfony\Component\HttpKernel\Kernel 
     */
    protected $kernel;

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

        assertNotNull($node, sprintf('Le champ caché "%s" n\'est pas présent', $field));

        $node->setValue($value);
    }

    /**
     * @Given /^je devrais voir l\'image "([^"]*)"$/
     */
    public function jeDevraisVoirLImage($imageName)
    {
        $page = $this->getSession()->getPage();
        $node = $page->find('css', 'img[title="' . $imageName . '"]');
        assertNotNull($node, sprintf('L\'image "%s" n\'est pas présente', $imageName));
    }

    /**
     * @Given /^je ne devrais pas voir l\'image "([^"]*)"$/
     */
    public function jeNeDevraisPasVoirLImage($imageName)
    {
        $page = $this->getSession()->getPage();
        $node = $page->find('css', 'img[title="' . $imageName . '"]');
        assertNull($node, sprintf('L\'image "%s" est présente', $imageName));
    }

    /**
     * @Given /^j\'appelle "([^"]*)" authentifié avec "([^"]*)"$/
     */
    public function jAppelleAuthentifieAvec($url, $username)
    {
        $wsseToken = $this->generateWsseToken($username);

        $session = $this->getSession();
        $session->setRequestHeader('HTTP_X_WSSE', $wsseToken);
        $session->visit($url);
    }

    /**
     * @Given /^j\'appelle du passé "([^"]*)" authentifié avec "([^"]*)"$/
     */
    public function jDuPasseAppelleAuthentifieAvec($url, $username)
    {
        $wsseToken = $this->generateWsseToken($username, gmdate('Y-m-d\TH:i:s\Z', time() - 3600));

        $session = $this->getSession();
        $session->setRequestHeader('HTTP_X_WSSE', $wsseToken);
        $session->visit($url);
    }

    /**
     * @Given /^j\'appelle du futur "([^"]*)" authentifié avec "([^"]*)"$/
     */
    public function jDuFuturAppelleAuthentifieAvec($url, $username)
    {
        $wsseToken = $this->generateWsseToken($username, gmdate('Y-m-d\TH:i:s\Z', time() + 3600));

        $session = $this->getSession();
        $session->setRequestHeader('HTTP_X_WSSE', $wsseToken);
        $session->visit($url);
    }

    /**
     * Generate Wsse token
     * 
     * @param String $username
     * @param String $password
     * @param String $timestamp
     * @param String $nonce
     * @return String
     */
    private function generateWsseToken($username, $timestamp = null, $nonce = null, $password = null)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('IOUserBundle:User')->findOneBy(array('username' => $username));
        assertNotNull($user, sprintf('L\'utilisateur "%s" n\'existe pas', $username));

        if ($timestamp === null) {
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        }
        if ($nonce === null) {
            $nonce = mt_rand();
        }
        if ($password === null) {
            $password = $user->getPassword();
        }

        $digest = base64_encode(sha1($nonce . $timestamp . $password, true));
        return sprintf('UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"', $username, $digest, base64_encode($nonce), $timestamp);
    }

    /**
     * @Given /^je post sur "([^"]*)" authentifié avec "([^"]*)" :$/
     */
    public function jePostSurAuthentifieAvec($url, $username, TableNode $table)
    {
        $data = array();
        foreach ($table->getHash() as $hash) {
            $key = $hash['key'];
            $value = $hash['value'];
            
            if (isset($data[$key])) {
                if (!is_array($data[$key])) {
                    $data[$key] = array($data[$key]);
                }
                $data[$key][] = $value;
            } else {
                $data[$key] = $value;
            }
        }

        $wsseToken = $this->generateWsseToken($username);
        $session = $this->getSession();
        $session->setRequestHeader('HTTP_X_WSSE', $wsseToken);

        $driver = $session->getDriver();
        $client = $driver->getClient();

        $class = new \ReflectionClass($driver);
        $prepareUrl = $class->getMethod('prepareUrl');
        $prepareUrl->setAccessible(true);
        $client->request('POST', $prepareUrl->invokeArgs($driver, array($url)), $data);
    }

    /**
     * @Given /^le json devrait convenir:$/
     */
    public function leJsonDevraitConvenir(TableNode $table)
    {
        $page = $this->getSession()->getDriver()->getContent();

        //print_r($page);

        $data = json_decode($page, true);
        assertNotNull($data, "Cannot parse json");

        $hash = $table->getHash();
        foreach ($hash[0] as $key => $value) {

            assertTrue(isset($data[$key]), sprintf('La clé "%s" n\'est pas présente dans le json', $key));
            if ($value === "FALSE") {
                assertFalse($data[$key], sprintf('La clé "%s" du json n\est pas "false"', $key));
            } elseif ($value === "TRUE") {
                assertTrue($data[$key], sprintf('La clé "%s" du json n\est pas "true"', $key));
            } else {
                assertTrue(strpos($value, $data[$key]) !== false, sprintf('La clé "%s" du json ne contient pas "%s"', $key, $value, $data[$key]));
            }
        }
    }

}
