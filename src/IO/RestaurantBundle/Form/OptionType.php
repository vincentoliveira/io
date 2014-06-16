<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class OptionType extends CarteItemType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        $builder
                ->remove('vat')
                ->remove('media')
                ->add('shortName', 'text', array(
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
