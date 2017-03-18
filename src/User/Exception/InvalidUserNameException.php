<?php

namespace SwayBundle\User\Exception;

class InvalidUserNameException extends \Exception
{
    public function __construct()
    {
        parent::__construct("User name is not valid");
    }

}

?>