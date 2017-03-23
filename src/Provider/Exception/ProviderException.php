<?php

namespace SwayBundle\Provider\Exception;

class ProviderException extends \Exception
{
    /**
     * Throws an exceptions when provider host and provider port are not specified
     * @return ProviderException
     */
    public static function emptyPlatformProvider() : ProviderException
    {
        return (new ProviderException("Missed platform provider credentials!"));
    }

    /**
     * Throws an exception when given provider's hostname is empty
     * @return ProviderException
     */
    public static function emptyProviderHostName() : ProviderException
    {
        return (new ProviderException("Given provider hostname cannot be empty!"));
    }

    /**
     * Throws an exception when given provider's service port is empty
     * @return ProviderException
     */
    public static function emptyProviderPort() : ProviderException
    {
        return (new ProviderException("Given provider port service cannot be empty! (provider_hostname:provider_port)"));
    }

    /**
     * Throws an exception when given provider's service port is invalid
     * @return ProvderException
     */
    public static function invalidProviderPort() : ProvderException
    {
        return (new ProviderException("Invalid provider's service port!"));
    }

    /**
     * Throws an exception when given platform app key is empty
     * @return ProviderException
     */
    public static function emptyPlatformAppKey(): ProviderException
    {
        return (new ProviderException("Given platform app key is empty!"));
    }
}

?>