<?php

namespace IO\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use IO\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->newUser($manager, 'admin', 'admin@innovorder.fr', array('ROLE_ADMIN'));
        $this->newUser($manager, 'resto', 'resto@innovorder.fr', array('ROLE_RESTAURATEUR'), 'Restaurant');
        $this->newUser($manager, 'cuisto', 'cuisto@innovorder.fr', array('ROLE_CUISINIER'), 'Restaurant');
    }

    protected function newUser(ObjectManager $manager, $username, $email, array $roles, $restaurantName = null)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPlainPassword($username);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setEnabled(true);
        
        if ($restaurantName) {
            $user->setRestaurant($this->getReference($restaurantName));
        }

        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}
