<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use IO\RestaurantBundle\Enum\ItemTypeEnum;

class DishType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // cela suppose que le gestionnaire d'entité a été passé en option
        $entityManager = $options['em'];
        $restaurant = $options['restaurant'];
        $transformer = new DataTransformer\MediaToNumberTransformer($entityManager);

        $builder
                ->add('name', 'text', array(
                    'label' => 'Nom du plat*',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Nom du plat',
                    ),
                    'constraints' => new \Symfony\Component\Validator\Constraints\NotBlank(array(
                        'message' => 'Veuillez renseigner un nom',
                            )),
                    'required' => true,
                ))
                ->add('description', 'textarea', array(
                    'label' => 'Description',
                    'attr' => array(
                        'class' => 'form-control',
                        'rows' => 2
                    ),
                    'required' => false,
                ))
                ->add($builder->create('media', 'hidden', array(
                            'attr' => array('class' => 'media-id'),
                        ))
                        ->addModelTransformer($transformer)
                )->add('shortName', 'text', array(
                    'label' => 'Nom court',
                    'attr' => array(
                        'class' => 'form-control',
                        'placeholder' => 'Nom court (affiché coté restaurant)',
                    ),
                    'required' => false,
                ))
                ->add('price', 'number', array(
                    'label' => 'Prix TTC (€)*',
                    'precision' => 2,
                    'attr' => array(
                        'class' => 'form-control'
                    ),
                    'required' => true,
                ))
                ->add('vat', 'entity', array(
                    'label' => 'TVA*',
                    'class' => 'IORestaurantBundle:ValueAddedTax',
                    'query_builder' => function(EntityRepository $er) use ($restaurant) {
                return $er->createQueryBuilder('vat')
                        ->select('vat')
                        ->where('vat.restaurant = :restaurant')
                        ->setParameter(':restaurant', $restaurant);
            },
                    'property' => 'nameAndValue',
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ))
                ->add('dishOptions', 'entity', array(
                    'label' => 'Options',
                    'attr' => array('class' => 'checkbox'),
                    'class' => 'IORestaurantBundle:CarteItem',
                    'query_builder' => function(EntityRepository $er) use ($restaurant) {
                return $er->createQueryBuilder('option')
                        ->select('option')
                        ->where('option.restaurant = :restaurant')
                        ->andWhere('option.itemType = :optionType')
                        ->setParameter(':restaurant', $restaurant)
                        ->setParameter(':optionType', ItemTypeEnum::TYPE_OPTION);
            },
                    'property' => 'name',
                    'multiple' => true,
                    'expanded' => true,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\RestaurantBundle\Entity\CarteItem'
        ));

        $resolver->setRequired(array(
            'em',
            'restaurant',
        ));

        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
            'restaurant' => 'IO\RestaurantBundle\Entity\Restaurant'
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
