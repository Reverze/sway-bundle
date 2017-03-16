<?php

namespace SwayBundle\User\Exception;

class InvalidEmailAddressException extends \Exception
{
    public function __construct(string $emailAddress)
    {
        parent::__construct(sprintf("Given email address '%s' is invalid", $emailAddress));
    }

}

?>