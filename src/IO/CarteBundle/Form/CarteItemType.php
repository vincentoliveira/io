<?php

namespace IO\CarteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IO\CarteBundle\Form\EventListener\RestaurantSubscriber;

/**
 * CarteItem Type
 */
abstract class CarteItemType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
                ->add('name', 'text', array(
                    'label' => 'Nom de la catégorie',
                    'attr' => array('class' => 'form-control'),
                    'constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(array(
                        'message' => 'Veuillez renseigner un nom',
                    )),
                    'required' => true,
                ))
                ->add('description', 'textarea', array(
                    'label' => 'Description',
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                ))
                ->add('file', 'file', array(
                    'label' => 'Image',
                    'required' => false,
                ));

        $builder->addEventSubscriber(new RestaurantSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\CarteBundle\Entity\CarteItem'
        ));
    }

}
