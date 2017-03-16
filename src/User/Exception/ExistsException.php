<?php

namespace SwayBundle\User\Exception;

class ExistsException extends \Exception
{
    public function __construct(string $userIdentifier)
    {
        parent::__construct("Account on exist on user identifier '$userIdentifier'");
    }
}


?>
