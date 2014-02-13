<?php

namespace IO\MenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IO\MenuBundle\Form\EventListener\RestaurantSubscriber;

/**
 * Menu Type
 */
class MenuType extends AbstractType
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
                    'label' => 'Nom du menu',
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ))
                ->add('description', 'text', array(
                    'label' => 'Description (optionnel)',
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ))
                ->add('price', 'number', array(
                    'label' => 'Prix de base (en EUR)',
                    'precision' => 2,
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ));
//                ->add('menuCategories', 'collection', array(
//                    'type' => 'text',
//                    'allow_add' => true,
//                    'allow_delete' => true,
//                    'options' => array(
//                        'required' => true,
//                        'attr' => array('class' => 'email-box')
//                    ),
//                ));

        $builder->addEventSubscriber(new RestaurantSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\MenuBundle\Entity\Menu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }

}
