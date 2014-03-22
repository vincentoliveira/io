<?php

namespace IO\CarteBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use IO\CarteBundle\Repository\CategoryRepository;
use IO\CarteBundle\Entity\Restaurant;

/**
 * Restaurant Subscriber
 */
class RestaurantSubscriber implements EventSubscriberInterface
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
        $data = $event->getData();
        $form = $event->getForm();

        if (!method_exists($data, 'getRestaurant')) {
            return;
        }
        
        $restaurant = $data->getRestaurant();
        if ($restaurant === null || !($restaurant instanceof Restaurant)) {
            return;
        }
        
        $fieldName = false;
        if (method_exists($data, 'getCategory')) {
            $fieldName = 'category';
        } else if (method_exists($data, 'getParent')) {
            $fieldName = 'parent';
        }

        // add parent/category field
        if ($fieldName) {
            $form->add($fieldName, 'entity', array(
                'label' => 'Categorie (parent)',
                'class' => 'IOCarteBundle:Category',
                'query_builder' => function(CategoryRepository $er) use ($restaurant) {
                    return $er->getRestaurantCategoryQueryBuilder($restaurant->getId());
                },
                'property' => 'name',
                'attr' => array('class' => 'form-control input-sm'),
                'required' => false,
            ));
        }
    }

}
