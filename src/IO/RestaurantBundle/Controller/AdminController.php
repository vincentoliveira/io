<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Form\RestaurantAndChiefType;
use IO\RestaurantBundle\Entity\Restaurant;
use IO\RestaurantBundle\Entity\RestaurantGroup;
use IO\RestaurantBundle\Entity\ValueAddedTax;
use IO\RestaurantBundle\Entity\CarteItem;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

/**
 * Admin User Controller
 * 
 * @Route("/admin/restaurant")
 */
class AdminController extends Controller
{

    /**
     * User token service
     * 
     * @Inject("io.auth_token_service")
     * @var \IO\ApiBundle\Service\AuthTokenService
     */
    public $userTokenSv;
    
    /**
     * Admin restaurant index
     * 
     * @return type
     * @Route("/", name="admin_restaurant_index")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $restaurantRepo = $em->getRepository("IORestaurantBundle:RestaurantGroup");
        $tokenRepo = $em->getRepository("IOApiBundle:AuthToken");

        $restaurants = $restaurantRepo->findAll();
        $tokens = array();
        foreach ($restaurants as $restaurantGroup) {
            foreach($restaurantGroup->getRestaurants() as $restaurant) {
                $tokens[$restaurantGroup->getId()][$restaurant->getId()] = $tokenRepo->findTokensForRestaurant($restaurant);
            }
        }
        
        return array(
            'restaurants' => $restaurants,
            'tokens' => $tokens,
        );
    }


    /**
     * Admin add restaurant
     * 
     * @return type
     * @Route("/new", name="admin_restaurant_new")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(new RestaurantAndChiefType());

        if ($request->isMethod("POST")) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $restaurant = $data["restaurant"];

                $group = new RestaurantGroup();
                $group->setName($restaurant->getName());
                $restaurant->setGroup($group);

                $chief = $data["chief"];
                $chief->setEnabled(true);
                $chief->setRestaurantGroup($group);
                $chief->addRole("ROLE_CHIEF");

                $em = $this->getDoctrine()->getManager();
                $em->persist($group);
                $em->persist($restaurant);
                $em->persist($chief);
                $em->flush();

                $this->createDefaultValue($restaurant);

                $session = $this->container->get('session');
                $session->getFlashBag()->add('success', sprintf('Le restaurant "%s" a bien été ajouté.', $group->getName()));

                return $this->redirect($this->generateUrl('admin_restaurant_index'));
            }
        }

        return array('form' => $form->createView());
    }
    
    
    /**
     * Create token for restaurant
     * 
     * @return type
     * @Route("/{id}/create_token", name="admin_restaurant_create_token")
     * @Secure(roles="ROLE_ADMIN")
     * @ParamConverter("restaurant", class="IORestaurantBundle:Restaurant")
     * @Template()
     */
    public function createTokenAction(Restaurant $restaurant)
    {
        $em = $this->getDoctrine()->getManager();
        
        $manager = $em->getRepository('IORestaurantBundle:Restaurant')->findFirstManager($restaurant);
        if ($manager !== null) {
            $this->userTokenSv->createToken($manager, array($restaurant), null);
        } else {
            $session = $this->container->get('session');
            $session->getFlashBag()->add('error', sprintf('Le restaurant "%s" n\'a pas de manager.', $restaurant->getName()));
        }
        
        return $this->redirect($this->generateUrl('admin_restaurant_index'));
    }


    protected function createDefaultValue(Restaurant $restaurant)
    {
        $em = $this->getDoctrine()->getManager();

        // VAT
        $defaultVatList = array(
            array(
                'name' => 'TVA Plats',
                'value' => 10.0,
            ),
            array(
                'name' => 'TVA Boissons',
                'value' => 10.0,
            ),
            array(
                'name' => 'TVA Alcools',
                'value' => 20.0,
            ),
        );
        foreach ($defaultVatList as &$vatValues) {
            $vat = new ValueAddedTax();
            $vat->setName($vatValues['name']);
            $vat->setValue($vatValues['value']);
            $vat->setRestaurant($restaurant);
            $em->persist($vat);

            $vatValues['entity'] = $vat;
        }


        // OPTION
        $defaultOptions = array(
            array(
                'name' => 'Sauces salades',
                'choices' => array(
                    array(
                        'name' => 'Vinaigrette maison',
                        'description' => 'Huile d\'olive, vinaigre de vin rouge, moutarde, sel, poivre',
                        'price' => 0,
                    ),
                    array(
                        'name' => 'Vinaigre balsamic',
                        'description' => 'Vinaigrette au vinaigre balsamic',
                        'price' => 0,
                    ),
                ),
            )
        );
        foreach ($defaultOptions as &$optionValue) {
            $option = new CarteItem();
            $option->setItemType(ItemTypeEnum::TYPE_OPTION);
            $option->setName($optionValue['name']);
            $option->setRestaurant($restaurant);
            foreach ($optionValue['choices'] as $choiceValue) {
                $choice = new CarteItem();
                $choice->setItemType(ItemTypeEnum::TYPE_OPTION_CHOICE);
                $choice->setName($choiceValue['name']);
                $choice->setDescription($choiceValue['description']);
                $choice->setPrice($choiceValue['price']);
                $choice->setRestaurant($restaurant);
                $choice->setParent($option);
                $em->persist($choice);
            }
            $em->persist($option);

            $optionValue['entity'] = $option;
        }

        // CATEGORY
        $defaultCategories = array(
            array(
                'name' => 'Nos salades',
                'description' => 'Décrouvrez nos salades fraiches!',
            ),
        );
        foreach ($defaultCategories as &$categoryValue) {
            $category = new CarteItem();
            $category->setItemType(ItemTypeEnum::TYPE_CATEGORY);
            $category->setName($categoryValue['name']);
            $category->setDescription($categoryValue['description']);
            $category->setRestaurant($restaurant);
            $em->persist($category);

            $categoryValue['entity'] = $category;
        }

        // CATEGORY
        $defaultProduct = array(
            array(
                'name' => 'Salade César',
                'shortName' => 'césar',
                'description' => 'Salade de poulet accompagnée de parmesan',
                'price' => 6.5,
                'vat' => 0,
                'option' => 0,
                'parent' => 0,
            ),
        );
        foreach ($defaultProduct as &$productValue) {
            $product = new CarteItem();
            $product->setItemType(ItemTypeEnum::TYPE_DISH);
            $product->setName($productValue['name']);
            $product->setShortName($productValue['shortName']);
            $product->setDescription($productValue['description']);
            $product->setPrice($productValue['price']);
            $product->setVat($defaultVatList[$productValue['vat']]['entity']);
            $product->addDishOption($defaultOptions[$productValue['option']]['entity']);
            $product->setParent($defaultCategories[$productValue['parent']]['entity']);
            $product->setRestaurant($restaurant);
            $em->persist($product);

            $vatValues['entity'] = $product;
        }

        $em->flush();
    }


}
