<?php

namespace SwayBundle\User\Exception;

class NameReservedException extends \Exception
{
    public function __construct(string $userName)
    {
        parent::__construct("Name '$userName' is currently used by another user");
    }
}


?>