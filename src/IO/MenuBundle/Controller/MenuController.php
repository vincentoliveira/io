<?php

namespace IO\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\Secure;

use IO\MenuBundle\Entity\Category;
use IO\MenuBundle\Entity\Dish;

/**
 * Menu Controller
 */
class MenuController extends Controller
{
    /**
     * Display home
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @Secure(roles="ROLE_CUISINIER")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $categories = $em->getRepository('IOMenuBundle:Category')->findBy(array('parent' => null));
        
        return array(
            'categories' => $categories,
        );
    }
    
    /**
     * Display home
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     * @Template()
     * @ParamConverter("category", class="IOMenuBundle:Category", options={"id" = "id"})
     * @Secure(roles="ROLE_CUISINIER")
     */
    public function showCategoryAction(Category $category)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $dishes = $em->getRepository('IOMenuBundle:Dish')->findBy(array('category' => $category));
        
        return array(
            'category' => $category,
            'dishes' => $dishes,
        );
    }
}