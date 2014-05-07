<?php

namespace IO\DefaultBundle\Features\Context;

use Behat\Gherkin\Node\TableNode;
use IO\RestaurantBundle\Entity\CarteItem;

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

    /**
     * @Given /^les items suivants existent:$/
     */
    public function lesItemsSuivantsExistent(TableNode $table)
    {
        $em = $this->getEntityManager();
        $restaurantRepository = $em->getRepository('IORestaurantBundle:Restaurant');

        $hash = $table->getHash();
        foreach ($hash as $subhash) {
            $item = new CarteItem();
            foreach ($subhash as $key => $value) {
                if ($key === 'Restaurant') {
                    $value = $restaurantRepository->findOneByName($value);
                }

                $method = 'set' . $key;
                
                $item->{$method}($value);
            }

            $em->persist($item);
        }

        $em->flush();
    }

}
