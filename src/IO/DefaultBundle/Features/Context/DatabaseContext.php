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
        $connection = $em->getConnection();
        $cmd = $em->getClassMetadata($entityName);
        $connection->exec('SET foreign_key_checks = 0;');
        $connection->exec(sprintf("TRUNCATE TABLE %s;", $cmd->getTableName()));
        $connection->exec('SET foreign_key_checks = 1;');
    }
}
