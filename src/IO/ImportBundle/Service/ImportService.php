<?php

namespace IO\ImportBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;

use IO\MenuBundle\Entity\Restaurant;
use IO\MenuBundle\Entity\Category;
use IO\MenuBundle\Entity\Dish;

/**
 * Import Service
 */
class ImportService
{
    /**
     *
     * @var EntityManager 
     */
    protected $em;
    
    /**
     *
     * @var Container 
     */
    protected $container;
    
    /**
     * Current category order
     *
     * @var integer 
     */
    private $categoryOrder;
    
    /**
     * Current dish order
     *
     * @var integer 
     */
    private $dishOrder;

    /**
     * Constructor
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /**
     * Import $restaurant menu from Wordpress 
     * 
     * @param \IO\MenuBundle\Entity\Restaurant $restaurant
     * @return array
     */
    public function import(Restaurant $restaurant)
    {
        $results = array();
        $results['success'] = false;
        
        $this->categoryOrder = 1;
        $this->dishOrder = 1;
        
        try {
            $results['logs'] = 'Start...' . "\n\n";
            
            $maxPage = 1;
            for ($page = 1; $page <= $maxPage; $page++) {
                $results['logs'] .= 'Connection to : ' . $restaurant->getWpBaseUrl();
                $jsonPosts = $this->requestWordpress($restaurant->getWpBaseUrl(), $page);
                $results['logs'] .= '... page ' . $page . ': OK';
                $results['logs'] .= "\n" . 'Results : ' . "\n" . $jsonPosts;

                if ($jsonPosts == false) {
                    $results['message'] = 'Echec de la récupération des données (no result)';
                    return $results;
                }

                $posts = json_decode($jsonPosts, true);
                if (!isset($posts['status']) || $posts['status'] !== 'ok') {
                    $results['message'] = 'Echec de la récupération des données (status: ko)';
                    return $results;
                }
                
                $maxPage = intval($posts['pages']);

                $data = $this->updateMenu($restaurant, $posts);

                $results['logs'] .= "\n\n" . 'Categories found : ' . "\n";
                foreach ($data['categories'] as $cat) {
                    $results['logs'] .= "\t" . $cat->getWpId() . ' => ' . $cat->getName() . "\n";
                }

                $results['logs'] .= "\n\n" . 'Dishes found : ' . "\n";
                foreach ($data['dishes'] as $dish) {
                    $results['logs'] .= "\t" . $dish->getWpId() . ' => ' . $dish->getName() . "\n";
                }
                
                $results['logs'] .= "\n\n+------------------------------------------------------------------------------+\n\n";
            }
            
            $results['logs'] .= 'End.';
            
            $results['success'] = true;
        } catch (\Exception $e) {
            $results['message'] = $e->getMessage();
        }
        
        return $results;
    }
    
    /**
     * Request wordpress JSON API get_posts
     * 
     * @param string $baseUrl
     * @return string|false
     * @throws \Exception
     */
    private function requestWordpress($baseUrl, $page = 1)
    {
        if ($this->container->get('kernel')->getEnvironment() === 'test') {
            $filepath = $this->container->get('kernel')->getRootDir() . '/../' . $baseUrl;
            if (!file_exists($filepath)) {
                return false;
            }
            return file_get_contents($filepath);
        }
        
        if (!function_exists('curl_version')) {
            throw new \Exception("Le service a besoin de l'extension CURL pour fonctionner", 1);
        }
        
        $url = $baseUrl . '?json=get_posts&count=50&page=' . $page;
        $proxy = $this->getProxy();
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($proxy) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }

        $result = curl_exec($curl);

        curl_close($curl);
        
        return $result;
    }
    
    /**
     * Get proxy
     * Return false if no proxy setted
     * 
     * @return string|false
     */
    private function getProxy()
    {
        if ($this->container->hasParameter('io_proxy') === false) {
            return false;
        }

        return $this->container->getParameter('io_proxy');
    }
    
    /**
     * Update Menu
     * 
     * @param \IO\MenuBundle\Entity\Restaurant $restaurant
     * @param array $posts
     */
    private function updateMenu(Restaurant $restaurant, array $posts)
    {
        $criteria = array('restaurant' => $restaurant);
        $categories = $this->em->getRepository('IOMenuBundle:Category')->findBy($criteria);
        $dishes = $this->em->getRepository('IOMenuBundle:Dish')->findBy($criteria);
        
        $newCategories = array();
        $newDishes = array();
        
        foreach ($posts['posts'] as $wpDish) {
            // get dish
            $dish = $this->findOrCreateDish($wpDish, $dishes, $restaurant);
            
            $newDishes[$dish->getWpId()] = $dish;
            
            // get category if exists
            if (empty($wpDish['categories']) === false) {
                $category = $this->findOrCreateCategory($wpDish['categories'][0], $categories, $restaurant);
                if ($category != null) {
                    $newCategories[$category->getWpId()] = $category;
                    $dish->setCategory($category);
                    
                    $this->em->persist($category);
                }
            }
            
            $this->em->persist($dish);
        }
        
//        // remove deleted categories
//        foreach ($categories as $category) {
//            if (!isset($newCategories[$category->getWpId()])) {
//                $this->em->remove($category);
//            }
//        }
//        
//        // remove deleted dishes
//        foreach ($dishes as $dish) {
//            if (!isset($newDishes[$dish->getWpId()])) {
//                $this->em->remove($dish);
//            }
//        }
        
        $this->em->flush();
        
        return array(
            'categories' => $newCategories,
            'dishes' => $newDishes,
        );
    }
    
    /**
     * Find dish
     * 
     * @param int $wpId
     * @param array $dishes
     * @param \IO\MenuBundle\Entity\Restaurant $restaurant
     * @return null|Dish
     */
    private function findOrCreateDish(array $wpDish, array &$dishes, Restaurant $restaurant)
    {
        foreach ($dishes as $dish) {
            if (intval($dish->getWpId()) === intval($wpDish['id'])) {
                return $this->setDishData($dish, $wpDish, $restaurant);
            }
        }
        
        $dish = new Dish();
        $dish = $this->setDishData($dish, $wpDish, $restaurant);
        $dish->setOrder($this->dishOrder);
        $this->dishOrder++;
        
        $dishes[] = $dish;
        return $dish;
    }

    /**
     * Set dish data from wp data
     * 
     * @param \IO\MenuBundle\Entity\Dish $dish
     * @param array $wpDish
     * @param array $dishes
     * @param \IO\MenuBundle\Entity\Restaurant $restaurant
     * @return \IO\MenuBundle\Entity\Dish
     */
    private function setDishData(Dish $dish, array $wpDish, Restaurant $restaurant)
    {
        $dish->setWpId($wpDish['id']);
        $dish->setRestaurant($restaurant);
        $dish->setName(html_entity_decode($wpDish['title'], ENT_NOQUOTES, 'UTF-8'));
        $dish->setDescription(html_entity_decode(strip_tags($wpDish['excerpt']), ENT_NOQUOTES, 'UTF-8'));
        $dish->setTags(implode(';', $wpDish['tags']));
        
        $imageUrlPattern = "/src=(\"\'??)(.*)(\"\'??)/Ui";
        if (preg_match($imageUrlPattern, $wpDish['content'], $matches)) {
            $dish->setImageUrl($matches[2]);
        }
        
        return $dish;
    }

    
    /**
     * Find or create category
     * 
     * @param array $wpCategory
     * @param array $categories
     * @param \IO\MenuBundle\Entity\Restaurant $restaurant
     * @return null|\IO\MenuBundle\Entity\Category
     */
    private function findOrCreateCategory(array $wpCategory, array &$categories, Restaurant $restaurant)
    {
        foreach ($categories as $category) {
            if (intval($category->getWpId()) === intval($wpCategory['id'])) {
                return $this->setCategoryData($category, $wpCategory, $categories, $restaurant);
            }
        }
        
        $category = new Category();
        $category = $this->setCategoryData($category, $wpCategory, $categories, $restaurant);
        $category->setOrder($this->categoryOrder);
        $this->categoryOrder++;
        
        $categories[] = $category;
        return $category;
    }
    
    /**
     * Set category data from wp data
     * 
     * @param \IO\MenuBundle\Entity\Category $category
     * @param array $wpCategory
     * @param array $categories
     * @param \IO\MenuBundle\Entity\Restaurant $restaurant
     * @return Category
     */
    private function setCategoryData(Category $category, array $wpCategory, array $categories, Restaurant $restaurant)
    {
        $category->setWpId($wpCategory['id']);
        $category->setRestaurant($restaurant);
        $category->setName(html_entity_decode($wpCategory['title']));
        
        $parentWpId = $wpCategory['parent'];
        if ($parentWpId && ($category->getParent() === null || $category->getParent()->getWpId() !== $parentWpId)) {
            foreach ($categories as $cat) {
                if ($cat->getWpId() === $parentWpId) {
                    $category->setParent($cat);
                    break;
                }
            }
        }
        
        return $category;
    }

}
