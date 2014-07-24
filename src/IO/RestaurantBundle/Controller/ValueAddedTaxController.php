<?php

namespace IO\RestaurantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use IO\RestaurantBundle\Entity\ValueAddedTax;
use IO\RestaurantBundle\Form\ValueAddedTaxType;

/**
 * VAT Controller.
 *
 * @Route("/vat")
 */
class ValueAddedTaxController extends Controller
{

    /**
     * User Service
     * 
     * @Inject("io.user_service")
     * @var \IO\UserBundle\Service\UserService
     */
    public $userSv;

    /**
     * List all taxes
     * 
     * @Route("/", name="vat_index")
     * @Secure(roles="ROLE_MANAGER")
     * @Template()
     */
    public function indexAction()
    {
        return $this->generateFormList();
    }

    /**
     * Create a tax
     * 
     * @Route("/create", name="vat_create")
     * @Secure(roles="ROLE_MANAGER")
     * @Method("POST")
     * @Template("IORestaurantBundle:ValueAddedTax:index.html.twig")
     */
    public function createAction(Request $request)
    {
        $restaurant = $this->userSv->getCurrentRestaurant();

        $vat = new ValueAddedTax();
        $vat->setRestaurant($restaurant);
        $form = $this->handleForm($request, $vat);
        
        if ($form->isValid()) {
            $form = null;
        }

        return $this->generateFormList($form);
    }

    /**
     * Update a tax
     * 
     * @Route("/update/{id}", name="vat_update")
     * @Secure(roles="ROLE_MANAGER")
     * @Method("POST")
     * @Template("IORestaurantBundle:ValueAddedTax:index.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $restaurant = $this->userSv->getCurrentRestaurant();

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("IORestaurantBundle:ValueAddedTax");
        $vat = $repo->findOneBy(array('id' => $id, 'restaurant' => $restaurant));

        if ($vat !== null) {
            $this->handleForm($request, $vat);
        }

        return $this->generateFormList();
    }

    /**
     * Delete a tax
     * 
     * @Route("/delete/{id}", name="vat_delete")
     * @Secure(roles="ROLE_MANAGER")
     * @Method("POST")
     * @Template("IORestaurantBundle:ValueAddedTax:index.html.twig")
     */
    public function deleteAction($id)
    {
        $restaurant = $this->userSv->getCurrentRestaurant();

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("IORestaurantBundle:ValueAddedTax");
        $vat = $repo->findOneBy(array('id' => $id, 'restaurant' => $restaurant));

        if ($vat !== null) {
            $em->remove($vat);
            $em->flush();
        }

        return $this->generateFormList();
    }

    /**
     * Generate Form List
     * 
     * @return array
     */
    private function generateFormList($createForm = null)
    {
        $formList = array();

        $restaurant = $this->userSv->getCurrentRestaurant();

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("IORestaurantBundle:ValueAddedTax");
        $vatList = $repo->findBy(array("restaurant" => $restaurant));
        foreach ($vatList as $vat) {
            $form = $this->createForm(new ValueAddedTaxType(), $vat);
            $formList[] = $form->createView();
        }

        if ($createForm === null) {
            $vat = new ValueAddedTax();
            $vat->setRestaurant($restaurant);
            $createForm = $this->createForm(new ValueAddedTaxType(), $vat);
        }
        $formList[] = $createForm->createView();

        return array('formList' => $formList);
    }

    /**
     * Handle create/update form and return json response
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \IO\RestaurantBundle\Entity\ValueAddedTax $vat
     * @return array
     */
    private function handleForm(Request $request, ValueAddedTax $vat)
    {
        $form = $this->createForm(new ValueAddedTaxType(), $vat);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vat);
            $em->flush();
        }

        return $form;
    }

}
