<?php

namespace SwayBundle\User\Exception;

class ConfirmCodeException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Error occurred while preparing confirm code!");
    }
}

?>