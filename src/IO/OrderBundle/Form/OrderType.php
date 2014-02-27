<?php

namespace IO\OrderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('status', 'choice', array(
                    'label' => 'Etat',
                    'attr' => array('class' => 'form-control'),
                    'choices' => \IO\OrderBundle\Entity\Order::$typeLotAdmin,
                ))
                ->add('tableName', 'text', array(
                    'label' => 'Table',
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                ))
                ->add('orderLines', 'collection', array(
                    'label' => false,
                    'type' => new OrderLineType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'options' => array(
                        'required' => true,
                        'em' => $options['em'],
                        'label' => false,
                    ),
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\OrderBundle\Entity\Order'
        ));

        $resolver->setRequired(array(
            'em',
        ));

        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'order';
    }

}
