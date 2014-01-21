<?php

namespace IO\ImportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * ImportFormType
 *
 */
class ImportFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder->add('restaurant', 'entity', array(
            'class' => 'IOMenuBundle:Restaurant',
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
