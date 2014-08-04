<?php

namespace IO\ApiBundle\Utils;

/**
 * ApiElement Interface
 * 
 * @author vincent
 */
interface ApiElement
{
    public function accept(ApiElementVisitorInterface $visitor);
}
