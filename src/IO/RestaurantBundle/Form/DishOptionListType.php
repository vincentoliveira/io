<?php

namespace IO\RestaurantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use IO\RestaurantBundle\Repository\CarteItemRepository;

/**
 * DishOptionList Type
 */
class DishOptionListType extends AbstractType {

    /**
     *
     * @var \IO\RestaurantBundle\Entity\Restaurant
     */
    protected $restaurant;

    public function __construct(\IO\RestaurantBundle\Entity\Restaurant $restaurant) {
        $this->restaurant = $restaurant;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);

        $restaurant = $this->restaurant;

        $builder->add('dish', 'entity', array(
            'label' => 'Plat',
            'class' => 'IORestaurantBundle:CarteItem',
            'attr' => array('class' => 'form-control'),
            'property' => 'name',
            'disabled' => true,
            'required' => true,
        ))->add('optionList', 'entity', array(
            'label' => 'Liste d\'option',
            'class' => 'IORestaurantBundle:CarteItem',
            'query_builder' => function(CarteItemRepository $er) use ($restaurant) {
                return $er->getRestaurantOptionListQueryBuilder($restaurant->getId());
            },
            'attr' => array('class' => 'form-control'),
            'property' => 'name',
            'required' => true,
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'IO\RestaurantBundle\Entity\DishOptionList'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'dish_option_list';
    }

}
