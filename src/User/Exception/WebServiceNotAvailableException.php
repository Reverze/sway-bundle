<?php

namespace SwayBundle\User\Exception;

use Exception;

class WebServiceNotAvailableException extends \Exception
{
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct("External webservice is not available!");
    }
}

?>