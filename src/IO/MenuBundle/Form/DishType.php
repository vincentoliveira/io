<?php

namespace IO\MenuBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DishType extends CarteItemType
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
                    'label' => 'Prix (€)',
                    'precision' => 2,
                    'attr' => array('class' => 'form-control'),
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\MenuBundle\Entity\Dish'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dish';
    }

}
