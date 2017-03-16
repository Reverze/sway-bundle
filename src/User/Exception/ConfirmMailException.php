<?php

namespace SwayBundle\User\Exception;

class ConfirmMailException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Unexpected error occurred while send confirmation mail!");
    }
}

?>