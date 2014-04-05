<?php

namespace IO\CarteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IO\CarteBundle\Form\DataTransformer\ArrayToStringDataTransformer;

class DishOptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', 'text', array(
                    'label' => 'Nom de l\'option',
                    'attr' => array('class' => 'form-control'),
                    'constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(array(
                        'message' => 'Veuillez renseigner un nom',
                            )),
                    'required' => true,
                ))
                ->add(
                        $builder->create('options', 'text', array(
                            'label' => 'Liste des choix possible séparés par des points-virgules',
                            'attr' => array('class' => 'radio'),
                            'constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(array(
                                'message' => 'Veuillez renseigner un nom',
                                    )),
                            'required' => true,
                        ))->addModelTransformer(new ArrayToStringDataTransformer()))
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\CarteBundle\Entity\DishOption'
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'io_cartebundle_dishoption';
    }


}
