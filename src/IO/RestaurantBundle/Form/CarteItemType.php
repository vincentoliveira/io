<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CarteItemType extends AbstractType
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
                    'label' => 'Nom',
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
                ->add('price', 'number', array(
                    'label' => 'Prix TTC (€)',
                    'precision' => 2,
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                ))
                ->add('vat', 'number', array(
                    'label' => 'TVA (%)',
                    'precision' => 2,
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                ))
                ->add('visible', 'checkbox', array(
                    'label' => 'Visible ?',
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                ))
                ->add('media', new MediaType(), array(
                    'label' => false,
                ))
        ;
        
        $builder->addEventSubscriber(new EventListener\ParentSubscriber());
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\RestaurantBundle\Entity\CarteItem'
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'carteitem';
    }


}
