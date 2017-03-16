<?php

namespace SwayBundle\User\Exception;

class PasswordHashException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Error occured while hash user's password");
    }
}

?>
