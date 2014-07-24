<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ValueAddedTaxType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                    'label' => 'Nom de la TVA',
                    'attr' => array('class' => 'form-control', 'placeholder' => "Nom de la TVA"),
                    'constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(array(
                        'message' => 'Veuillez renseigner le nom de la TVA',
                    )),
                    'required' => true,
                ))
            ->add('value', 'number', array(
                    'label' => 'Valeur de la TVA (%)',
                    'precision' => 2,
                    'attr' => array('class' => 'form-control', 'placeholder' => "Taux"),
                    'required' => true,
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\RestaurantBundle\Entity\ValueAddedTax'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vat';
    }
}
