<?php

namespace SwayBundle\User\Exception;

use Exception;

class EmptyUserNameException extends \Exception
{
    public function __construct()
    {
        parent::__construct("User name is empty");
    }
}

?>