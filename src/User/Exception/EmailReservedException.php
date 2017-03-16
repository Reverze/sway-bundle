<?php

namespace SwayBundle\User\Exception;

class EmailReservedException extends \Exception
{
    public function __construct(string $emailAddress) 
    {
        parent::__construct("Email address '$emailAddress' is assigned to another account");
    }
}

?>
