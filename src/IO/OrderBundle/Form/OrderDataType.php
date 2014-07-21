<?php

namespace IO\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderDataType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('orderDate', 'datetime', array(
                    'label' => 'Date de livraison',
                    'widget' => 'single_text',
                    'input' => 'datetime',
                    'format' => 'dd-MM-yyyy HH:mm',
                    'attr' => array('class' => 'form-control datepicker'),
                    'required' => true,
                ))
                ->add('comment', 'textarea', array(
                    'label' => 'Commentaires',
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                ))
                ->add('customer', new CustomerType())
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'IO\OrderBundle\Entity\OrderData'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'order';
    }

}
