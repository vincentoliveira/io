<?php

namespace IO\CarteBundle\Features\Context;

use IO\CarteBundle\Entity\Restaurant;
use IO\CarteBundle\Entity\Category;
use IO\CarteBundle\Entity\Dish;
use IO\OrderBundle\Entity\Order;

/**
 * Database context.
 */
class DatabaseContext extends AbstractContext
{
    /**
     * @Given /^je supprime tous les "([^"]*)"$/
     */
    public function jeSupprimeTousLes($entityName)
    {
        $em = $this->getEntityManager();
        $entities = $em->getRepository($entityName)->findAll();

        foreach ($entities as $entity) {
            $em->remove($entity);
        }

        $em->flush();
    }
    
    /**
     * @Given /^je crée une catégorie "([^"]*)" pour le restaurant "([^"]*)"$/
     */
    public function jeCreeUneCategoryPourLeRestaurant($categoryName, $restaurantName)
    {
        $em = $this->getEntityManager();
        $restaurant = $em->getRepository('IOCarteBundle:Restaurant')->findOneBy(array('name' => $restaurantName));
        assertNotNull($restaurant, sprintf('Le restaurant "%s" n\'existe pas', $restaurantName));
        
        $category = new Category();
        $category->setName($categoryName);
        $category->setRestaurant($restaurant);
        $category->setOrder(0);
        
        $em->persist($category);
        $em->flush();
    }

    /**
     * @Given /^je crée un plat "([^"]*)" dans la category "([^"]*)" du "([^"]*)"$/
     */
    public function jeCreeUnPlatDansLaCategoryDu($dishName, $categoryName, $restaurantName)
    {
        $em = $this->getEntityManager();
        $restaurant = $em->getRepository('IOCarteBundle:Restaurant')->findOneBy(array('name' => $restaurantName));
        $category = $em->getRepository('IOCarteBundle:Category')->findOneBy(array('name' => $categoryName, 'restaurant' => $restaurant));
        assertNotNull($category, sprintf('La category "%s" du restaurant n\'existe pas', $categoryName, $restaurantName));
        
        $dish = new Dish();
        $dish->setName($dishName);
        $dish->setRestaurant($restaurant);
        $dish->setCategory($category);
        $dish->setOrder(0);
        
        $em->persist($dish);
        $em->flush();
    }
    
    /**
     * @Given /^le restaurant "([^"]*)" existe avec l\'url "([^"]*)"$/
     */
    public function leRestaurantExisteAvecLUrl($name, $wpUrl)
    {
        $em = $this->getEntityManager();
        $restaurant = $em->getRepository('IOCarteBundle:Restaurant')->findOneBy(array('name' => $name));
        if ($restaurant === null) {
            $restaurant = new Restaurant();
            $restaurant->setName($name);
        }
        
        $restaurant->setWpBaseUrl($wpUrl);
        
        $em->persist($restaurant);
        $em->flush();
    }
    
    
    /**
     * @Given /^il n\'y a aucune commande$/
     */
    public function ilNYAAucuneCommande()
    {
        $em = $this->getEntityManager();
        $entities = $em->getRepository('IOOrderBundle:Order')->findAll();

        foreach ($entities as $entity) {
            $em->remove($entity);
        }

        $em->flush();
        
        $connection = $em->getConnection();
        $connection->exec("ALTER TABLE order_item AUTO_INCREMENT = 1;");
    }


    /**
     * @Given /^il y a une commande en cours pour "([^"]*)"$/
     */
    public function ilYAUneCommandeEnCoursPour($restaurantName)
    {
        $em = $this->getEntityManager();
        $restaurant = $em->getRepository('IOCarteBundle:Restaurant')->findOneBy(array('name' => $restaurantName));
        assertNotNull($restaurant, sprintf('Le restaurant "%s" n\'existe pas', $restaurantName));
        
        $order = new Order();
        $order->setRestaurant($restaurant);
        
        $em->persist($order);
        $em->flush();
    }
}
