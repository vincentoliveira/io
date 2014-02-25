<?php

namespace IO\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use \IO\OrderBundle\Entity\Order;
use \IO\OrderBundle\Entity\OrderLine;

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
     * - <strong>POST</strong> {order:{table_name:"table_name","items":[{id:"item_id"},{id:"item_id"},...]}
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

        $tableName = $request->request->get('table_name');
        $items = $request->request->get('items');

        if ($items === null || empty($items)) {
            return new JsonResponse(array('status' => 'ko', 'reason' => 'Nothing to order'));
        }

        $order = new Order();
        $order->setRestaurant($user->getRestaurant());
        $order->setTableName($tableName);

        $em = $this->getDoctrine()->getManager();

        foreach ($items as $item) {
            $data = explode(':', $item, 2);
            if (count($data) !== 2) {
                return new JsonResponse(array('status' => 'ko', 'reason' => 'Bad item'));
            }

            $itemType = $data[0];
            $itemId = $data[1];

            if (!(OrderLine::$itemTypeToEntity[$itemType])) {
                return new JsonResponse(array('status' => 'ko', 'reason' => 'Bad item'));
            }
            $entityName = OrderLine::$itemTypeToEntity[$itemType];
            $itemEntity = $em->getRepository($entityName)->find($itemId);
            if ($itemEntity === null) {
                return new JsonResponse(array('status' => 'ko', 'reason' => 'Bad item'));
            }

            $line = new OrderLine();
            $line->setOrder($order);
            $line->setItemId($itemId);
            $line->setItemType($itemType);
            $line->setItemPrice($itemEntity->getPrice());
            $em->persist($line);
        }

        $em->persist($order);
        $em->flush();

        $orderSv = $this->container->get('order.order');
        return new JsonResponse(array(
                    'status' => 'ok',
                    'order' => $orderSv->getJsonArray($order),
                ));
    }

}
