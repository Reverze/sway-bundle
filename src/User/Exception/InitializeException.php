<?php

namespace SwayBundle\User\Exception;

class InitializeException extends \Exception 
{
    public function __construct() 
    {
        parent::__construct("Cannot not initialize SWUser object. Maybe class not found");
    }
}

?>
