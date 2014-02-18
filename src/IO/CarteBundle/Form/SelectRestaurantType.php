<?php

namespace IO\CarteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SelectRestaurantType
 *
 */
class SelectRestaurantType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('restaurant', 'entity', array(
            'class' => 'IOCarteBundle:Restaurant',
            'property' => 'name',
            'attr' => array('class' => 'form-control')
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'select';
    }

}
