<?php

namespace IO\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;


/**
 * API Category controller
 */
class CategoryController extends Controller
{
    /**
     * Get restaurant categories
     * WSSE : <strong>ON</strong>
     * Parameters :
     * - <strong>GET</strong> <em>restaurant</em> restaurant name
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Secure(roles="ROLE_TABLETTE")
     */
    public function getCategoriesAction(Request $request)
    {
        $user = $this->get('user.user_service')->getUser();
        if ($user === null) {
            throw $this->createNotFoundException('getCategoriesAction: Unauthentified');
        }
        
        if ($user->hasRole('ROLE_ADMIN')) {
            $restaurantName = $request->query->get('restaurant');
        } else {
            $restaurantName = $user->getRestaurant()->getName();
        }
        
        if (empty($restaurantName)) {
            return new JsonResponse(array('status' => 'ko'));
        }
        
        $results = $this->get('menu.category')->getRestaurantCategories($restaurantName);
        
        return new JsonResponse(array(
            'status' => 'ok',
            'categories' => $results
        ));
    }
}
