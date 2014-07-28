<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

class OptionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $em = $options['em'];
        
        $builder
                ->add('itemType', 'hidden', array(
                    'data' => ItemTypeEnum::TYPE_OPTION,
                ))
                ->add('name', 'text', array(
                    'label' => 'Nom de la liste d\'options*',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Nom de la liste d\'option',
                    ),
                    'constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(array(
                        'message' => 'Veuillez renseigner un nom',
                            )),
                    'required' => true,
                ))
                ->add('children', 'collection', array(
                    'label' => false,
                    'type' => new OptionChoiceType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'options' => array('em' => $em),
        ));
    }

    /**
     * {@inheritdoc}
     */
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
    public function getName()
    {
        return 'dish';
    }

}
