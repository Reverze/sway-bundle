<?php

namespace SwayBundle\User\Exception;

class AccountNotConfirmedException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Action not available! Account is not confirmed!");
    }
}

?>