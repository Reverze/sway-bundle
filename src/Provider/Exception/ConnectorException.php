<?php

namespace SwayBundle\Provider\Exception;

class ConnectorException extends \Exception
{
    /**
     * Throws an exception when no parameters were given to provider connector
     * @return ConnectorException
     */
    public static function emptyParameters() : ConnectorException
    {
        return (new ConnectorException("No parameters were given for provider connector!"));
    }

    /**
     * Throws an exception when parameter is missing in connection parameters
     * @param string $parameterName
     * @return ConnectorException
     */
    public static function missedConnectionParameter(string $parameterName) : ConnectorException
    {
        return (new ConnectorException(sprintf("Missed parameter '%s'!")));
    }
}

?>