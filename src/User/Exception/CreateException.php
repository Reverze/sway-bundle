<?php

namespace SwayBundle\User\Exception;

class CreateException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Store a new user into database failed");
        
    }
}

?>

