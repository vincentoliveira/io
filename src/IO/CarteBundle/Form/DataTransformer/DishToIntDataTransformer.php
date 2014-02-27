<?php

namespace IO\CarteBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use IO\CarteBundle\Entity\Dish;

class DishToIntDataTransformer implements DataTransformerInterface
{

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (Dish) to a string (id).
     *
     * @param  Dish|null $issue
     * @return string
     */
    public function transform($dish)
    {
        if (null === $dish) {
            return "";
        }

        return $dish->getId();
    }

    /**
     * Transforms a string (id) to an object (Dish).
     *
     * @param  string $number
     * @return Dish|null
     * @throws TransformationFailedException if object (dish) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $dish = $this->om
                ->getRepository('IOCarteBundle:Dish')
                ->findOneBy(array('id' => $id))
        ;

        if (null === $dish) {
            throw new TransformationFailedException(sprintf('Le plat "#%s" ne peut pas être trouvé!', $id));
        }

        return $dish;
    }

}