<?php

namespace SwayBundle\Provider\Exception;

class MissedConfigurationParameterException extends \Exception
{
    public function __construct(string $parameterName)
    {
        parent::__construct(sprintf("Parameter '%s' is missed or empty!", $parameterName));
    }

}

?>