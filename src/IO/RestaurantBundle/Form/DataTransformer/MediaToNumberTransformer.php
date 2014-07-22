<?php

namespace IO\RestaurantBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use IO\RestaurantBundle\Entity\Media;

/**
 * Description of MediaToNumberTransformer
 *
 * @author vincent
 */
class MediaToNumberTransformer implements DataTransformerInterface {

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }

    /**
     * Transforms an object (media) to a string (number).
     *
     * @param  Media|null $media
     * @return string
     */
    public function transform($media) {
        if (null === $media) {
            return "";
        }

        return $media->getId();
    }

    /**
     * Transforms a string (number) to an object (media).
     *
     * @param  string $id
     * @return Media|null
     * @throws TransformationFailedException if object (media) is not found.
     */
    public function reverseTransform($id) {
        if (!$id) {
            return null;
        }

        $media = $this->om
                ->getRepository('IORestaurantBundle:Media')
                ->find($id)
        ;

        if (null === $media) {
            throw new TransformationFailedException(sprintf(
                    'Le media #%s ne peut pas être trouvé!', $id
            ));
        }

        return $media;
    }

}
