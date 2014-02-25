<?php

namespace IO\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * API Order controller
 */
class OrderController extends Controller
{

    /**
     * Get restaurant dishes
     * WSSE : <strong>ON</strong>
     * Parameters :
     * - <strong>GET</strong> <em>order_id</em> order ID
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Secure(roles="ROLE_TABLETTE")
     */
    public function getOrderAction(Request $request)
    {
        $user = $this->get('user.user_service')->getUser();
        if ($user === null) {
            throw $this->createNotFoundException('getDishesAction: Unauthentified');
        }

        if ($user->hasRole('ROLE_ADMIN')) {
            $restaurantName = $request->query->get('restaurant');
        } else {
            $restaurantName = $user->getRestaurant()->getName();
        }

        if (empty($restaurantName)) {
            return new JsonResponse(array('status' => 'ko'));
        }

        $orderId = $request->query->get('order_id', 0);
        $order = $this->getDoctrine()->getRepository('IOOrderBundle:Order')->find($orderId);
        if ($order === null ||
                (!$user->hasRole('ROLE_ADMIN') &&
                $order->getRestaurant()->getId() != $user->getRestaurant()->getId())) {
            return new JsonResponse(array(
                        'status' => 'ko',
                        'reason' => 'This order does not exist or you cannot see it',
                        'order' => $order ? $order->getId() : null,
                        'order_restaurant' => $order ? $order->getRestaurant()->getId() : null,
                        'user_restaurant' => $user->getRestaurant()->getId(),
                    ));
        }

        $orderSv = $this->container->get('order.order');
        return new JsonResponse(array(
                    'status' => 'ok',
                    'order' => $orderSv->getJsonArray($order),
                ));
    }

    /**
     * Get restaurant categories
     * WSSE : <strong>ON</strong>
     * Parameters :
     * - <strong>POST</strong> {commande:{table_name:"table_name","items":[{id:"item_id"},{id:"item_id"},...]}
     * 
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Secure(roles="ROLE_TABLETTE")
     */
    public function addAction(Request $request)
    {
        $user = $this->get('user.user_service')->getUser();
        if ($user === null) {
            throw $this->createNotFoundException('getCategoriesAction: Unauthentified');
        }

        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return new JsonResponse(array(
                        'status' => 'ko',
                        'reason' => 'Cannot parse JSON',
                    ));
        }

        //$restaurantName = $user->getRestaurant()->getName();

        return new JsonResponse(array(
                    'status' => 'ok',
                ));
    }

}
