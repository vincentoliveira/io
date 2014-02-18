<?php

namespace IO\CarteBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use IO\CarteBundle\Entity\Restaurant;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->newRestaurant($manager, 'Restaurant', 'http://37.187.65.120/wordpress/');
        $this->newRestaurant($manager, 'Sushi shop', 'http://37.187.65.120/wordpress/sushi/');
        $this->newRestaurant($manager, 'Cosy shop', 'http://37.187.65.120/wordpress/cosysushi/');
        $this->newRestaurant($manager, 'Blend', 'http://37.187.65.120/wordpress/dlemb/');
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
