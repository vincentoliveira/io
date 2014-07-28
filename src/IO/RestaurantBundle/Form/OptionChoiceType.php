<?php

namespace IO\RestaurantBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

class OptionChoiceType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        
        // cela suppose que le gestionnaire d'entité a été passé en option
        $entityManager = $options['em'];
        $transformer = new DataTransformer\MediaToNumberTransformer($entityManager);
        
        $builder
                ->add('itemType', 'hidden', array(
                    'data' => ItemTypeEnum::TYPE_OPTION_CHOICE,
                ))
                ->add('name', 'text', array(
                    'label' => 'Nom de l\'option*',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Nom de l\'option',
                    ),
                    'constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(array(
                        'message' => 'Veuillez renseigner un nom',
                            )),
                    'required' => true,
                ))
                ->add($builder->create('media', 'hidden', array(
                            'attr' => array('class' => 'media-id'),
                        ))
                        ->addModelTransformer($transformer)
                )
                ->add('price', 'number', array(
                    'label' => 'Prix TTC (€)*',
                    'precision' => 2,
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Prix (supplément)',
                    ),
                    'required' => true,
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
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
        return 'dish';
    }

}
