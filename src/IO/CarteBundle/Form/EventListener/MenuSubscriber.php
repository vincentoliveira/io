<?php

namespace IO\CarteBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Menu Subscriber
 */
class MenuSubscriber implements EventSubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    /**
     * Add restaurant hidden field
     * 
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $menu = $event->getData();

        if (!$menu instanceof \IO\CarteBundle\Entity\Menu) {
            return;
        }
        
        $categories = $menu->getMenuCategories();
        if ($categories !== null && !$categories->isEmpty()) {
            return;
        }
        
        $menuCategory = new \IO\CarteBundle\Entity\MenuCategory();
        $menuCategory->setMenu($menu);
        $menuCategory->setRestaurant($menu->getRestaurant());
        $menuCategory->setOrder(0);
        $menu->addMenuCategorie($menuCategory);
    }

}
