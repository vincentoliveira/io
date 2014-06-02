<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class CategoryType extends CarteItemType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
                ->remove('price')
                ->add('vat', 'number', array(
                    'label' => 'TVA par défaut (%)',
                    'data' => 10.0,
                    'precision' => 2,
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                ))
        ;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'category';
    }


}
