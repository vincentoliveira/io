<?php

namespace IO\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * StatFilter Type
 */
class StatFilterType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('start_date', 'date', array(
            'label' => 'Début',
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'attr' => array('class' => 'form-control'),
            'required' => false,
        ))->add('end_date', 'date', array(
            'label' => 'Fin',
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'attr' => array('class' => 'form-control'),
            'required' => false,
        ));
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'stat_filter';
    }

}
