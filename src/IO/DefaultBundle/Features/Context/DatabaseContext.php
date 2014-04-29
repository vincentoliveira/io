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
     * @Given /^je vide les entités "([^"]*)"$/
     */
    public function jeVideLesEntites($entityName)
    {
        $em = $this->getEntityManager();
        $entities = $em->getRepository($entityName)->findAll();

        foreach ($entities as $entity) {
            $em->remove($entity);
        }

        $em->flush();
        
        $connection = $em->getConnection();
        $cmd = $em->getClassMetadata($entityName);
        $connection->exec(sprintf("ALTER TABLE %s AUTO_INCREMENT = 1;", $cmd->getTableName()));
    }
}
