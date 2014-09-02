<?php

namespace IO\UserBundle\Enum;

/**
 * Description of GenderEnum
 */
class GenderEnum
{
    const GENDER_MALE = 'MALE';
    const GENDER_FEMALE = 'FEMALE';
    
    static $genders = array(self::GENDER_MALE, self::GENDER_FEMALE);
}
