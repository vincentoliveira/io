<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * RestaurantGroupType
 *
 */
class RestaurantGroupType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('name', 'text', array(
            'label' => 'Nom du groupe/restaurant',
            'attr' => array('class' => 'form-control'),
            'required' => true,
            'constraints' => new NotBlank(array('message' => 'Veuillez renseigner le nom du groupe/restaurant')),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\RestaurantBundle\Entity\RestaurantGroup'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'restaurant_group';
    }

}
