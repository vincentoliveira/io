<?php

namespace IO\DefaultBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\Behat\Console\BehatApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

use IO\UserBundle\Entity\User;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\RestaurantBundle\Entity\RestaurantGroup;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Entity\Media;
use IO\ApiBundle\Entity\AuthToken;
use IO\OrderBundle\Entity\OrderData;
use IO\OrderBundle\Entity\OrderLine;

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
     * @return Restaurant
     */
    protected function getRestaurant($restaurantName = 'phpunittest')
    {
        $repo = $this->em->getRepository('IORestaurantBundle:Restaurant');

        $restaurant = $repo->findOneBy(array('name' => $restaurantName));
        if ($restaurant === null) {
            $restaurantGroup = new RestaurantGroup();
            $restaurantGroup->setName($restaurantName);
            
            $restaurant = new Restaurant();
            $restaurant->setName($restaurantName);
            $restaurant->setGroup($restaurantGroup);
            
            $this->em->persist($restaurant);
            $this->em->persist($restaurantGroup);
            $this->em->flush();
        }

        return $restaurant;
    }
    
    /**
     * Get or generate token for restaurant
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @return \IO\ApiBundle\Entity\AuthToken
     */
    protected function getTokenForRestaurant(Restaurant $restaurant)
    {
        $tokenTok = 'token-' . $restaurant->getId();
        
        $repo = $this->em->getRepository('IOApiBundle:AuthToken');
        $token = $repo->findOneByToken($tokenTok);
        if ($token === null) {
            $token = new AuthToken();
            $token->setExpireAt(null);
            $token->setToken($tokenTok);
            $token->addRestrictedRestaurant($restaurant);
            $this->em->persist($token);
            $this->em->flush();
        }
        
        return $token;
    }
    
    /**
     * Return product
     * 
     * @param type $productName
     * @param type $categoryName
     * @param type $restaurant
     * @return \IO\RestaurantBundle\Entity\CarteItem
     */
    protected function productExistInCategoryForRestaurant($productName, $categoryName, $restaurant)
    {
        $repo = $this->em->getRepository('IORestaurantBundle:CarteItem');
        $product = $repo->findOneByName($productName);
        if ($product === null) {
            $product = new CarteItem();
            $product->setItemType(\IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_DISH);
            $product->setRestaurant($restaurant);
            $product->setName($productName);
            $product->setShortName($productName);
            $product->setPrice(1.0);
            $product->setVisible(true);
        }
                
        $category = $repo->findOneByName($categoryName);
        if ($category === null) {
            $category = new CarteItem();
            $category->setItemType(\IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_CATEGORY);
            $category->setRestaurant($restaurant);
            $category->setName($categoryName);
            $category->setShortName($categoryName);
            $category->setVisible(true);
            $this->em->persist($category);
        }
        
        $product->setParent($category);
        $this->em->persist($product);
        $this->em->flush();
        
        return $product;
    }

    /**
     * Create empty cart
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     * @param \IO\ApiBundle\Entity\AuthToken $token
     * @return \IO\OrderBundle\Entity\OrderData
     */
    protected function createCart(Restaurant $restaurant, AuthToken $token)
    {
        $orderService = $this->container->get('io.order_service');
        return $orderService->createOrder($restaurant, $token);
    }
    
    /**
     * Return new chart
     * 
     * @param \IO\RestaurantBundle\Entity\CarteItem $product
     * @param \IO\OrderBundle\Entity\OrderData $cart
     * @return \IO\OrderBundle\Entity\OrderData
     */
    protected function addProductToCart(CarteItem $product, OrderData $cart)
    {
        $orderService = $this->container->get('io.order_service');
        $cart = $orderService->addProductToOrder($cart, $product->getId());
        return $cart;
    }
    
    /**
     * 
     * @param type $optionName
     * @param array $choices
     * @param \IO\RestaurantBundle\Entity\CarteItem $product
     */
    protected function createOptionForProduct($optionName, array $choices, CarteItem $product)
    {
        $repo = $this->em->getRepository('IORestaurantBundle:CarteItem');
        $option = $repo->findOneByName($optionName);
        if ($option === null) {
            $option = new CarteItem();
            $option->setItemType(\IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_OPTION);
            $option->setRestaurant($product->getRestaurant());
            $option->setName($optionName);
            $option->setShortName($optionName);
            $option->setVisible(true);
            
            foreach ($choices as $choiceName) {
                $choice = new CarteItem();
                $choice->setItemType(\IO\RestaurantBundle\Enum\ItemTypeEnum::TYPE_OPTION_CHOICE);
                $choice->setRestaurant($product->getRestaurant());
                $choice->setParent($option);
                $choice->setName($choiceName);
                $choice->setShortName($choiceName);
                $choice->setVisible(true);
                $choice->setPrice(0);
                
                $option->addChild($choice);
                $this->em->persist($choice);
            }
        }
        
        if (!$product->getDishOptions()->contains($option)) {
            $product->addDishOption($option);
        }
        
        $this->em->persist($option);
        $this->em->flush();
        
        return $option;
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
        
        $tables = array($cmd->getTableName());
        foreach ($cmd->getAssociationMappings() as $associationMapping) {
            if (isset($associationMapping['joinTable']) && 
                    isset($associationMapping['joinTable']['name'])) {
                $tables[] = $associationMapping['joinTable']['name'];
            }
        }
        
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            foreach ($tables as $table) {
                $q = $dbPlatform->getTruncateTableSql($table);
                $connection->executeUpdate($q);
            }
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
        }
        $user->setEmail($username . '@io.fr');
        $user->setPlainPassword($username);
        $user->setEnabled(true);
        
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

