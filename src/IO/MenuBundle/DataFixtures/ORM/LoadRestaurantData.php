<?php

namespace IO\MenuBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use IO\MenuBundle\Entity\Restaurant;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->newRestaurant($manager, 'Restaurant', 'http://37.187.65.120/wordpress');
        $this->newRestaurant($manager, 'Restaurant2', 'http://37.187.65.120/wordpress');
    }

    protected function newRestaurant(ObjectManager $manager, $name, $wpBaseUrl)
    {
        $restaurant = new Restaurant();
        $restaurant->setName($name);
        $restaurant->setWpBaseUrl($wpBaseUrl);
        
        $this->addReference($name, $restaurant);

        $manager->persist($restaurant);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}
