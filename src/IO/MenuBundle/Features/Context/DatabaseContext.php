<?php

namespace IO\MenuBundle\Features\Context;

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
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $entities = $em->getRepository($entityName)->findAll();

        foreach ($entities as $entity) {
            $em->remove($entity);
        }

        $em->flush();
    }
    
    /**
     * @Given /^le restaurant "([^"]*)" existe avec l\'url "([^"]*)"$/
     */
    public function leRestaurantExisteAvecLUrl($name, $wpUrl)
    {        
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        
        $restaurant = $em->getRepository('IOMenuBundle:Restaurant')->findOneBy(array('name' => $name));
        if ($restaurant === null) {
            $restaurant = new \IO\MenuBundle\Entity\Restaurant();
            $restaurant->setName($name);
        }
        
        $restaurant->setWpBaseUrl($wpUrl);
        
        $em->persist($restaurant);
        $em->flush();
    }

}
