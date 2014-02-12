<?php

namespace IO\MenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use IO\MenuBundle\Entity\Restaurant;

/**
 * Menu Type
 */
class MenuType extends AbstractType
{

    /**
     * Current user
     * 
     * @var Restaurant
     */
    private $restaurant;

    /**
     * Set restaurant
     * 
     * @param Restaurant $restaurant
     * @return \IO\MenuBundle\Form\MenuType
     */
    public function setRestaurant(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $restaurantId = $this->restaurant !== null ? $this->restaurant->getId() : null;

        $builder
                ->add('name', 'text', array(
                    'label' => 'Nom du menu',
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ))
                ->add('description', 'text', array(
                    'label' => 'Description (optionnel)',
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ))
                ->add('price', 'number', array(
                    'label' => 'Prix de base (en EUR)',
                    'precision' => 2,
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ))
                ->add('category', 'entity', array(
                    'class' => 'IOMenuBundle:Category',
                    'query_builder' => function(EntityRepository $er) use ($restaurantId) {
                        return $er->getRestaurantCategoryQueryBuilder($restaurantId);
                    },
                    'property' => 'name',
                    'attr' => array('class' => 'form-control'),
                    'required' => true,
                ));
//                ->add('menuCategories', 'collection', array(
//                    'type' => 'text',
//                    'allow_add' => true,
//                    'allow_delete' => true,
//                    'options' => array(
//                        'required' => true,
//                        'attr' => array('class' => 'email-box')
//                    ),
//                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'IO\MenuBundle\Entity\Menu'
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
