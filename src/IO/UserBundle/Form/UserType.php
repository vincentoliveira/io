<?php

namespace IO\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * UserType
 *
 */
class UserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('username', 'text', array(
                    'label' => 'Username',
                    'attr' => array('class' => 'form-control'),
                    'constraints' => array(
                        new NotBlank(array('message' => 'Veuillez renseigner le username')),
                )))
                ->add('email', 'text', array(
                    'label' => 'Email',
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('plainPassword', 'password', array(
                    'label' => 'Password',
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('roles', 'choice', array(
                    'label' => 'Role',
                    'attr' => array('class' => 'form-control'),
                    'choices' => array(
                        'ROLE_TABLET' => 'Tablette',
                        'ROLE_CUISINIER' => 'Cuisinier',
                        'ROLE_SERVEUR' => 'Serveur',
                        'ROLE_MANAGER' => 'Manager',
                    ),
                    'multiple' => true,
                    'expanded' => false,
                    'required' => false,
                ))
                ->add('restaurant', 'entity', array(
                    'label' => 'Restaurant',
                    'class' => 'IORestaurantBundle:Restaurant',
                    'property' => 'name',
                    'attr' => array('class' => 'form-control'),
                    'required' => false,
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\USerBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user';
    }

}
