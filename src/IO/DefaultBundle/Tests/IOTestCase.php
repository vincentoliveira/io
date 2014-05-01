<?php

namespace IO\DefaultBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Description of IOTestCase
 */
class IOTestCase extends WebTestCase
{

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


}

