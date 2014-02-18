<?php

namespace IO\CarteBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use \Symfony\Component\Form\Form;

/**
 * FormError Service
 */
class FormErrorService
{

    /**
     * @param \Symfony\Component\Form\Form $form
     * @return array
     */
    public function getAllFormErrorMessages(Form $form)
    {
        $errorMessages = $form->getErrorsAsString();
        $errorMessageLines = explode("\n", $errorMessages);

        $errors = array();
        foreach ($errorMessageLines as $line) {
            $found = strstr($line, "ERROR: ");
            if ($found !== false) {
                $errors[] = substr($found, 7);
            }
        }

        return array_unique($errors);
    }


}
