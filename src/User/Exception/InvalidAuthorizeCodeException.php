<?php

namespace SwayBundle\User\Exception;

class InvalidAuthorizeCodeException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Invalid authorize code!");
    }
}

?>