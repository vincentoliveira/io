<?php

namespace IO\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * HistoriqueFilter Type
 */
class HistoriqueFilterType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
                ->add('dateFrom', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'Entre le',
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('dateTo', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'Et le',
                    'attr' => array('class' => 'form-control'),
                ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'filters';
    }

}
