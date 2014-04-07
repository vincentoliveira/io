<?php

namespace IO\CarteBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToStringDataTransformer implements DataTransformerInterface
{
    const DELIMITER = ";";
    
    /**
     * Transforms an array to a string.
     *
     * @param  array|null $array
     * @return string
     */
    public function transform($array)
    {
        if (null === $array) {
            return "";
        }

        return implode(sprintf(' %s ', self::DELIMITER), $array);
    }

    /**
     * Transforms a string to an array.
     *
     * @param  string $string
     * @return array
     */
    public function reverseTransform($string)
    {
        $array = explode(self::DELIMITER, $string);
        
        foreach ($array as &$data) {
            $data = trim($data);
        }
        
        return $array;
    }

}