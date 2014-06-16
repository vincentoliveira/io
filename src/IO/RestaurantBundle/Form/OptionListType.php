<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class OptionListType extends CarteItemType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder
                ->remove('parent')
                ->remove('price')
                ->remove('vat')
                ->remove('media')
                ->remove('visible');
        
        $builder->addEventSubscriber(new EventListener\RemoveParentSubscriber());
    }

    /**
     * @return string
     */
    public function getName() {
        return 'dish';
    }

}
