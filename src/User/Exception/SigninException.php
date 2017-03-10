<?php

namespace SwayBundle\User\Exception;

class SigninException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unexpected error occured');
    }
    
}


?>

