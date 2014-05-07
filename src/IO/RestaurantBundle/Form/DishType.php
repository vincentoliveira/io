<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

class DishType extends CarteItemType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'dish';
    }


}
