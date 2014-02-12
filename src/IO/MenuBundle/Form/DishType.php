<?php

namespace IO\MenuBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use IO\MenuBundle\Entity\Restaurant;

class DishType extends AbstractType
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
                    'label' => 'Nom du plat',
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('description', 'textarea', array(
                    'label' => 'Description',
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('price', 'number', array(
                    'label' => 'Prix (€)',
                    'precision' => 2,
                    'attr' => array('class' => 'form-control'),
                ))
                ->add('category', 'entity', array(
                    'class' => 'IOMenuBundle:Category',
                    'query_builder' => function(EntityRepository $er) use ($restaurantId) {
                        return $er->getRestaurantCategoryQueryBuilder($restaurantId);
                    },
                    'property' => 'name',
                    'attr' => array('class' => 'form-control')
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
