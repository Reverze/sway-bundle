<?php

namespace SwayBundle\User\Exception;

use Exception;

class ResourceNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Resource was not found!");
    }
}

?>