<?php

namespace IO\DefaultBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Behat\Console\BehatApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

use IO\UserBundle\Entity\User;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;

/**
 * Description of IOTestCase
 */
class IOTestCase extends WebTestCase
{

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function setUp()
    {
        parent::setUp();

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->container = static::$kernel->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        
        $this->client = static::createClient(array('environment' => 'test'));
        $this->client->followRedirects();
    }
    
    /**
     * Behavior Test Suite
     * 
     * @param type $bundle
     */
    protected function behaviorTestSuite($bundleName = '@IODefaultBundle')
    {
        $input = new ArrayInput(array(
                    '--ansi' => '',
                    '--verbose' => '',
                    '--format' => 'progress',
                    'features' => $bundleName,
                ));
        $output = new ConsoleOutput();
        $app = new BehatApplication('DEV');
        $app->setAutoExit(false);
        $result = $app->run($input, $output);
        
        $this->assertEquals(0, $result);
    }

    /**
     * Get restaurant
     * 
     * @return \IO\CarteBundle\Entity\Restaurant
     */
    protected function getRestaurant($restaurantName = 'phpunittest')
    {
        $repo = $this->em->getRepository('IOCarteBundle:Restaurant');

        $restaurant = $repo->findOneBy(array('name' => $restaurantName));
        if ($restaurant === null) {
            $restaurant = new \IO\CarteBundle\Entity\Restaurant();
            $restaurant->setName($restaurantName);
            $this->em->persist($restaurant);
            $this->em->flush();
        }

        return $restaurant;
    }

    /**
     * Remove all occurence of $entityName
     * 
     * @param string $entityName
     */
    protected function truncate($entityName)
    {
        $cmd = $this->em->getClassMetadata($entityName);
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

    /**
     * Find or create user and its restaurant
     * @param string $username
     * @param string $restaurantName
     * @param string $role
     * @return User
     */
    public function userExists($username, $restaurantName = null, $role = null)
    {
        $user = $this->em->getRepository('IOUserBundle:User')->findOneByUsername($username);
        if ($user === null) {
            $user = new User();
            $user->setUsername($username);
            $user->setEmail($username . '@io.fr');
            $user->setPlainPassword($username);
        }
        
        if ($restaurantName != null) {
            $restaurant = $this->em->getRepository('IORestaurantBundle:Restaurant')->findOneByName($restaurantName);
            if ($restaurant === null) {
                $restaurant = new Restaurant();
                $restaurant->setName($restaurantName);
                $this->em->persist($restaurant);
            }
            $user->setRestaurant($restaurant);
        }
        
        if ($role != null) {
            $user->addRole($role);
        }
        
        $this->em->persist($user);
        $this->em->flush();
        
        return $user;
    }

    /**
     * Find and delete a user
     * 
     * @param string $username
     * @return User
     */
    public function userDoesNotExists($username)
    {
        $user = $this->em->getRepository('IOUserBundle:User')->findOneByUsername($username);
        if ($user !== null) {
            $this->em->remove($user);
            $this->em->flush();
        }
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
    protected function generateWsseToken($username, $password = null, $timestamp = null, $nonce = null)
    {
        if ($password === null) {
            $user = $this->em->getRepository('IOUserBundle:User')->findOneByUsername($username);
            $password = $user->getPassword();
        }
        
        if ($timestamp === null) {
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        }
        if ($nonce === null) {
            $nonce = mt_rand();
        }

        $digest = base64_encode(sha1($nonce . $timestamp . $password, true));
        return sprintf('UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"', $username, $digest, base64_encode($nonce), $timestamp);
    }

    /**
     * Insert carte items into database
     * 
     * @param array $items
     */
    protected function insertCarteItems(array $items)
    {        
        $restaurantRepository = $this->em->getRepository('IORestaurantBundle:Restaurant');

        $itemList = array();
        $mediaList = array();
        foreach ($items as $key => $data) {
            $item = new CarteItem();
            foreach ($data as $field => $value) {
                $setter = 'set' . ucfirst($field);
                if ($field === 'parent') {
                    $value = $itemList[$value];
                } elseif ($field === 'restaurant') {
                    $value = $restaurantRepository->findOneByName($value);
                } elseif ($field === 'media') {
                    if (isset($mediaList[$value])) {
                        $value = $mediaList[$value];
                    } else {
                        $media = new Media();
                        $media->setPath($value);
                        $this->em->persist($media);
                        $value = $media;
                    }
                }
                $item->{$setter}($value);
            }
            $this->em->persist($item);
            $itemList[$key] = $item;
        }
        $this->em->flush();
    }
    
}

