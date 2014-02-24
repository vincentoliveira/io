<?php

namespace IO\CarteBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Menu Type
 */
class MenuType extends CarteItemType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
                ->add('price', 'number', array(
                    'label' => 'Prix de base (en EUR)',
                    'precision' => 2,
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ))
                ->add('menuCategories', 'collection', array(
                    'type' => new MenuCategoryType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'options' => array(
                        'required' => true,
                    ),
                ));
        
        $builder->addEventSubscriber(new EventListener\MenuSubscriber());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\CarteBundle\Entity\Menu'
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
