<?php

namespace SwayBundle\User\Exception;

class ClientAuthenticationException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Cannot not authenticate client");
    }
}

?>