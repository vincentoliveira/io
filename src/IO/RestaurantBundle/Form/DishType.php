<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class DishType extends CarteItemType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder->add('shortName', 'text', array(
            'label' => 'Nom court',
            'attr' => array('class' => 'form-control'),
            'required' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'dish';
    }

}
