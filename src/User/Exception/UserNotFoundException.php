<?php

namespace SwayBundle\User\Exception;

use Exception;

class UserNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct("User was not found!");
    }
}

?>