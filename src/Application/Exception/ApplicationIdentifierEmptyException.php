<?php

namespace SwayBundle\Application\Exception;

class ApplicationIdentifierEmptyException extends \Exception
{
    public function __construct ()
    {
        parent::__construct("Application identifier cannot be empty");
    }
    
}

?>

