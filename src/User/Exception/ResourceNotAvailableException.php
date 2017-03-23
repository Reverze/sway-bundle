<?php

namespace SwayBundle\User\Exception;


class ResourceNotAvailableException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Resource is not available");
    }
}

?>