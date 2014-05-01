<?php

namespace IO\DefaultBundle\Features\Context;

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
        
        $connection = $em->getConnection();
        $connection->exec("ALTER TABLE order_item AUTO_INCREMENT = 1;");
    }
}
