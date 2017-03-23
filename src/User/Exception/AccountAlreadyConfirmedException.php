<?php

namespace SwayBundle\User\Exception;

use Exception;

class AccountAlreadyConfirmedException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Operation is not permitted. User account is already confirmed!");
    }
}

?>