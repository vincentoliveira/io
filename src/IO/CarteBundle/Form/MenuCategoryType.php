<?php

namespace IO\CarteBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * MenuCategory Type
 */
class MenuCategoryType extends CarteItemType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
                ->remove('description')
                ->remove('file');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\CarteBundle\Entity\MenuCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu_category';
    }

}
