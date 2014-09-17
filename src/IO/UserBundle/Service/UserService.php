<?php

namespace IO\UserBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\DependencyInjection\Container;
use IO\UserBundle\Entity\User;
use IO\UserBundle\Entity\UserIdentity;
use IO\UserBundle\Entity\PhoneNumber;
use IO\UserBundle\Entity\Address;
use IO\UserBundle\Entity\UserWallet;
use IO\UserBundle\Enum\GenderEnum;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\ApiBundle\Utils\BadParameterException;
/**
 * User Service
 * 
 * @Service("io.user_service")
 */
class UserService
{

    /**
     * Container
     * 
     * @Inject("service_container")
     * @var Container
     */
    public $container;
    
    /**
     * Container
     * 
     * @Inject("session")
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    public $session;

    /**
     * Entity Manager
     * 
     * @Inject("doctrine.orm.entity_manager")
     * @var \Doctrine\ORM\EntityManager
     */
    public $em;

    /**
     * Fos User Manager
     * 
     * @Inject("fos_user.user_manager")
     * @var \FOS\UserBundle\Model\UserManager
     */
    public $userManager;
    
    /**
     * Get loggued user
     *
     * @return \IO\UserBundle\Entity\User|null
     */
    public function getUser()
    {
        $token = $this->container->get('security.context')->getToken();
        $user = $token !== null ? $token->getUser() : null;

        return $user instanceof User ? $user : null;
    }
    
    /**
     * Get loggued user restaurant
     *
     * @return \IO\RestaurantBundle\Entity\User|null
     */
    public function getCurrentRestaurant()
    {
        $user = $this->getUser();
        if ($user instanceof User && !$user->hasRole("ROLE_CHIEF") && !$user->hasRole("ROLE_ADMIN")) {
            return $user->getRestaurant();
        }
        
        $session = $this->session;
        $id = $session->get("user.restaurant");
        if ($id === null) {
            return null;
        }
        
        return $this->em->getRepository('IORestaurantBundle:Restaurant')->find($id);
    }
    
    /**
     * Authentification user
     *
     * @return User
     */
    public function authUserData(array $data)
    {
        if (!isset($data['email']) || !isset($data['plainPassword'])) {
            return null;
        }
        
        $repo = $this->em->getRepository('IOUserBundle:User');
        $user = $repo->findOneByEmail($data['email']);
        if ($user === null || !$user->isEnabled()) {
            return null;
        }
        
        $hashPwd = $user->getPassword();
        $user->setPlainPassword($data['plainPassword']);
        $this->userManager->updatePassword($user);
        if ($hashPwd !== $user->getPassword()) {
            return null;
        }
        
        return $user;
    }
    
    
    /**
     * Create a user from array
     * 
     * @param array $data
     * @return \IO\UserBundle\Entity\User
     * @throws BadParameterException
     */
    public function createUser(array $data)
    {
        $user = new User();
        
        $requiredFields = array('email', 'plainPassword');
        $missingFields = array();
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            } else {
                $setter = 'set' . ucfirst($field);
                $user->{$setter}($data[$field]);
            }
        }
        
        if (!empty($missingFields)) {
            throw new BadParameterException(sprintf('Missing parameters: %s', implode(', ', $missingFields)));
        }
        $user->setUsername($data['email']);
        $user->setEnabled(true);
        
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        } else {
            $user->addRole('ROLE_CLIENT');
        }
        
        return $user;
    }
    
    /**
     * Create a user identity from array
     * 
     * @param array $data
     * @return \IO\UserBundle\Entity\UserIdentity
     * @throws BadParameterException
     */
    public function createUserIdentity(array $data)
    {
        return $this->editUserIdentity(new UserIdentity(), $data);
    }
    
    /**
     * Edit a user identity from array
     * 
     * @param UserIdentity $userIdentity
     * @param array $data
     * @return \IO\UserBundle\Entity\UserIdentity
     * @throws BadParameterException
     */
    public function editUserIdentity(UserIdentity $userIdentity = null, array $data = array())
    {
        if (!$userIdentity) {
            $userIdentity = new UserIdentity();
        }
        
        $requiredFields = array('lastname', 'firstname', 'email', 'birthdate');
        $missingFields = array();
        foreach ($requiredFields as $field) {
            if (!$userIdentity->{'get' . ucfirst($field)}() &&
                (!isset($data[$field]) || empty($data[$field]))) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            throw new BadParameterException(sprintf('Missing parameters: %s', implode(', ', $missingFields)));
        }
        
        if (isset($data[$field]) && !empty($data[$field])) {
            $birthdate = \DateTime::createFromFormat('Y-m-d', $data['birthdate']);
            if ($birthdate === false) {
                throw new BadParameterException('Bad parameter: birthdate');
            } else {
                $data['birthdate'] = $birthdate;
            }
        }
        
        foreach ($requiredFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $setter = 'set' . ucfirst($field);
                $userIdentity->{$setter}($data[$field]);
            }
        }
        
        if (isset($data['gender']) && isset(GenderEnum::$genders[$data['gender']])) {
            $userIdentity->setGender(GenderEnum::$genders[$data['gender']]);
        }
        
        return $userIdentity;
    }
    
    /**
     * Create a phone number from array
     * 
     * @param array $data
     * @return \IO\UserBundle\Entity\PhoneNumber
     * @throws BadParameterException
     */
    public function createPhoneNumber(array $data)
    {
        return $this->editPhoneNumber(new PhoneNumber(), $data);
    }
    
    /**
     * Edit a phone number from array
     * 
     * @param PhoneNumber $phoneNumber 
     * @param array $data
     * @return \IO\UserBundle\Entity\PhoneNumber
     * @throws BadParameterException
     */
    public function editPhoneNumber(PhoneNumber $phoneNumber = null, array $data = array())
    {
        if (!$phoneNumber) {
            $phoneNumber = new PhoneNumber();
        }
        
        $requiredFields = array('prefix', 'number');
        $missingFields = array();
        foreach ($requiredFields as $field) {
            if (!$phoneNumber->{'get' . ucfirst($field)}() && 
                (!isset($data[$field]) || empty($data[$field]))) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            throw new BadParameterException(sprintf('Missing parameters: %s', implode(', ', $missingFields)));
        }
        
        if (isset($data['number']) && !empty($data['number'])) {
            $number = preg_replace('/(\W*)/', '', $data['number']);
            $phoneNumber->setNumber($number);
        }
        if (isset($data['prefix']) && !empty($data['prefix'])) {
            $phoneNumber->setPrefix($data['prefix']);
        }
        
        return $phoneNumber;
    }
    /**
     * Create a user wallet from array
     * 
     * @param array $data
     * @return \IO\UserBundle\Entity\PhoneNumber
     * @throws BadParameterException
     */
    public function createWallet(array $data)
    {
        return $this->editWallet(new UserWallet(), $data);
    }
    
    /**
     * Edit a user wallet from array
     * 
     * @param array $data
     * @return \IO\UserBundle\Entity\PhoneNumber
     * @throws BadParameterException
     */
    public function editWallet(UserWallet $wallet = null, array $data = array())
    {
        if (!$wallet) {
            $wallet = new UserWallet();
        }
        
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $wallet->setUserId($data['user_id']);
        }
        if (isset($data['wallet_id']) && !empty($data['wallet_id'])) {
            $wallet->setWalletId($data['wallet_id']);
        }
        
        return $wallet;
    }
    
    /**
     * Create an address from array
     * 
     * @param array $data
     * @return \IO\UserBundle\Entity\PhoneNumber
     * @throws BadParameterException
     */
    public function createAddress(array $data)
    {
        return $this->editAddress(new Address(), $data);
    }
    
    /**
     * Edit an address from array
     * 
     * @param Address $address
     * @param array $data
     * @return \IO\UserBundle\Entity\PhoneNumber
     * @throws BadParameterException
     */
    public function editAddress(Address $address = null, array $data = array())
    {
        if (!$address) {
            $address = new Address();
        }
        
        $requiredFields = array('number', 'street', 'postcode', 'city', 'country');
        $missingFields = array();
        foreach ($requiredFields as $field) {
            if (!$address->{'get' . ucfirst($field)}() &&
                (!isset($data[$field]) || empty($data[$field]))) {
                $missingFields[] = $field;
            } else if (isset($data[$field]) && !empty($data[$field])) {
                $setter = 'set' . ucfirst($field);
                $address->{$setter}($data[$field]);
            }
        }
        
        if (!empty($missingFields)) {
            throw new BadParameterException(sprintf('Missing parameters: %s', implode(', ', $missingFields)));
        }
        
        $mandatoryFields = array('name', 'building', 'staircase', 'floor', 'digicode', 'intercom', 'comment');
        foreach ($mandatoryFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $setter = 'set' . ucfirst($field);
                $address->{$setter}($data[$field]);
            }
        }
        
        return $address;
    }
    
    /**
     * Set current user restaurant
     * 
     * @param \IO\RestaurantBundle\Entity\Restaurant $restaurant
     */
    public function setCurrentRestaurant(Restaurant $restaurant)
    {
        $session = $this->session;
        $session->set("user.restaurant", $restaurant->getId());
        $session->save();
    }

}
