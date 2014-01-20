<?php

namespace IO\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class IOUserBundle extends Bundle
{
    
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
