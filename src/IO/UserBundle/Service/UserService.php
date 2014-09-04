<?php

namespace IO\UserBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\DependencyInjection\Container;
use IO\UserBundle\Entity\User;
use IO\UserBundle\Entity\UserIdentity;
use IO\UserBundle\Entity\PhoneNumber;
use IO\UserBundle\Entity\Address;
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
     * User token service
     * 
     * @Inject("io.auth_token_service")
     * @var \IO\ApiBundle\Service\AuthTokenService
     */
    public $userTokenSv;
    
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
     * @return \IO\ApiBundle\Entity\AuthToken
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
        
        return $this->userTokenSv->createToken($user);
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
        $requiredFields = array('gender', 'lastname', 'firstname', 'email', 'birthdate');
        $missingFields = array();
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            throw new BadParameterException(sprintf('Missing parameters: %s', implode(', ', $missingFields)));
        }
        
        if (!isset(GenderEnum::$genders[$data['gender']])) {
            throw new BadParameterException('Bad parameter: gender');
        }
        
        $birthdate = \DateTime::createFromFormat('d/m/Y', $data['birthdate']);
        if ($birthdate === false) {
            throw new BadParameterException('Bad parameter: birthdate');
        }
        
        $userIdentity = new UserIdentity();
        $userIdentity->setGender(GenderEnum::$genders[$data['gender']]);
        $userIdentity->setLastname($data['lastname']);
        $userIdentity->setFirstname($data['firstname']);
        $userIdentity->setEmail($data['email']);
        $userIdentity->setBirthdate($birthdate);
        
        return $userIdentity;
    }
    
    /**
     * Create a user identity from array
     * 
     * @param array $data
     * @return \IO\UserBundle\Entity\PhoneNumber
     * @throws BadParameterException
     */
    public function createPhoneNumber(array $data)
    {
        $requiredFields = array('prefix', 'number');
        $missingFields = array();
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (!empty($missingFields)) {
            throw new BadParameterException(sprintf('Missing parameters: %s', implode(', ', $missingFields)));
        }
        
        $number = preg_replace('/(\W*)/', '', $data['number']);
        
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setPrefix($data['prefix']);
        $phoneNumber->setNumber($number);
        
        return $phoneNumber;
    }
    
    /**
     * Create a user identity from array
     * 
     * @param array $data
     * @return \IO\UserBundle\Entity\PhoneNumber
     * @throws BadParameterException
     */
    public function createAddress(array $data)
    {
        $address = new Address();
        
        $requiredFields = array('number', 'street', 'postcode', 'city', 'country');
        $missingFields = array();
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            } else {
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
