<?php

namespace SwayBundle\Controller\Exception;

class ControllerException extends \Exception
{
    /**
     * Throws an exception when mail service is missed
     * @param string $serviceName
     * @return \SwayBundle\Controller\Exception\ControllerException
     */
    public static function mailerServiceMissed(string $serviceName) : ControllerException
    {
        return (new ControllerException(sprintf("Mailer service '%s' is missed", $serviceName)));
    }
}

?>
