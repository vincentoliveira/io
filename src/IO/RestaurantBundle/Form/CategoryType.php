<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType {

    /**
     * Build form
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        
        // cela suppose que le gestionnaire d'entité a été passé en option
        $entityManager = $options['em'];
        $transformer = new DataTransformer\MediaToNumberTransformer($entityManager);

        $builder
                ->add('name', 'text', array(
                    'label' => 'Nom de la catégorie*',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Nom de la catégorie',
                    ),
                    'constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(array(
                        'message' => 'Veuillez renseigner un nom',
                    )),
                    'required' => true,
                ))
                ->add('description', 'textarea', array(
                    'label' => 'Description',
                    'attr' => array('class' => 'form-control', 'rows' => 2),
                    'required' => false,
                ))
                ->add(
                        $builder->create('media', 'hidden', array(
                            'attr' => array('class' => 'media-id'),
                        ))
                        ->addModelTransformer($transformer)
                )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'IO\RestaurantBundle\Entity\CarteItem'
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
    public function getName() {
        return 'category';
    }

}
