<?php

namespace SwayBundle\User\Exception;

class InvalidPasswordException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Password is invalid");
    }
}


?>

