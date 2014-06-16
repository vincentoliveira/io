<?php

namespace IO\RestaurantBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use IO\RestaurantBundle\Repository\CarteItemRepository;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

/**
 * Parent Subscriber
 */
class ParentSubscriber implements EventSubscriberInterface {

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    /**
     * Add restaurant hidden field
     * 
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSetData(FormEvent $event) {
        $data = $event->getData();
        $form = $event->getForm();

        $restaurant = $data->getRestaurant();
        if ($restaurant === null) {
            return;
        }

        if ($data->getItemType() === ItemTypeEnum::TYPE_OPTION) {
            $form->add('parent', 'entity', array(
                'label' => 'Liste d\'option',
                'class' => 'IORestaurantBundle:CarteItem',
                'query_builder' => function(CarteItemRepository $er) use ($restaurant) {
                    return $er->getRestaurantOptionListQueryBuilder($restaurant->getId());
                },
                'property' => 'name',
                'attr' => array('class' => 'form-control input-sm'),
                'required' => false,
            ));
        } else {
            $form->add('parent', 'entity', array(
                'label' => 'Categorie (parent)',
                'class' => 'IORestaurantBundle:CarteItem',
                'query_builder' => function(CarteItemRepository $er) use ($restaurant) {
                    return $er->getRestaurantCategoryQueryBuilder($restaurant->getId());
                },
                'property' => 'name',
                'attr' => array('class' => 'form-control input-sm'),
                'required' => false,
            ));
        }
    }

}
