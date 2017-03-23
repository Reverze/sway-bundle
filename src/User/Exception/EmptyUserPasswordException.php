<?php

namespace SwayBundle\User\Exception;

class EmptyUserPasswordException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Given user password was empty");
    }
}

?>