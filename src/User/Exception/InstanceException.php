<?php

namespace SwayBundle\User\Exception;

class InstanceException extends \Exception
{
    public function __construct() 
    {
        parent::__construct("Global variable 'SW_USER' is not instance of 'SWUser' class");
    }
}


?>
