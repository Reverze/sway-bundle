<?php

namespace SwayBundle\Provider;

use SwayBundle\Provider\Exception\ConnectorException;
use XA\PlatformClient;

class Connector
{
    /**
     * Connect parameters
     * @var array
     */
    private $connectParameters = array();

    /**
     * XAUser environment
     * @var \XA\PlatformClient\Controller\User\XAUserEnvironment
     */
    private $xaUserEnvironment = null;

    /**
     * Generic XAUser
     * @var \XA\PlatformClient\Controller\User\XAUserGeneric
     */
    private $xaUserGeneric = null;

    /**
     * Current XAUser
     * @var \XA\PlatformClient\Controller\User\XAUser
     */
    private $xaUserCurrent = null;

    public function __construct(array $parameters)
    {
        if (empty($parameters)){
            throw ConnectorException::emptyParameters();
        }

        if (!isset($parameters['hostname'])){
            throw ConnectorException::missedConnectionParameter('hostname');
        }

        if (!isset($parameters['port'])){
            throw ConnectorException::missedConnectionParameter('port');
        }

        if (!isset($parameters['appkey'])){
            throw ConnectorException::missedConnectionParameter('appkey');
        }

        if (!isset($parameters['cachedriver'])){
            throw ConnectorException::missedConnectionParameter('cachedriver');
        }

        if (!isset($parameters['lifetimemultiplier'])){
            throw ConnectorException::missedConnectionParameter('lifetimemultiplier');
        }

        $this->connectParameters = $parameters;
    }

    /**
     * Connects with platform provider
     */
    public function connect()
    {
        $platformCredentials = new PlatformClient\Auth\PlatformCredentials();
        $platformCredentials->setAppKey($this->connectParameters['appkey']);
        $platformCredentials->setProvider($this->connectParameters['hostname']);
        $platformCredentials->setPort($this->connectParameters['port']);

        $cacheDriverParameters = new PlatformClient\Cache\CacheDriverParameters();
        $cacheDriverParameters->setDriver($this->connectParameters['cachedriver']);
        $cacheDriverParameters->setMultiplier($this->connectParameters['lifetimemultiplier']);
        $cacheDriverParameters->setParameters($this->connectParameters['driverparameters'] ?? array());

        $core = new PlatformClient\Core();
        $core->setProvider($platformCredentials);
        $core->setCacheParameters($cacheDriverParameters);

        $core->connect();

        $this->connectParameters = array();

        $this->initializeUser();
    }

    /**
     * Initializes platform user
     */
    protected function initializeUser()
    {
        /**
         * XAUserEnvironment represents users environment
         */
        $this->xaUserEnvironment = new PlatformClient\Controller\User\XAUserEnvironment();

        /**
         * XAUserGeneric represents generic XAUser
         */
        $this->xaUserGeneric = new PlatformClient\Controller\User\XAUserGeneric();

        /**
         * XAUserCurrent represents current XAUser
         */
        $this->xaUserCurrent = new PlatformClient\Controller\User\XAUser($this->xaUserEnvironment,
            $this->xaUserGeneric);

        /**
         * Wakeups XAUser
         */
        $this->xaUserCurrent->wakeup();
    }

    /**
     * Gets XAUserEnvironment instance
     * @return PlatformClient\Controller\User\XAUserEnvironment
     */
    public function getUserEnvironment() : PlatformClient\Controller\User\XAUserEnvironment
    {
        return $this->xaUserEnvironment;
    }

    /**
     * Gets generic XAUser
     * @return PlatformClient\Controller\User\XAUserGeneric
     */
    public function getUserGeneric() : PlatformClient\Controller\User\XAUserGeneric
    {
        return $this->xaUserGeneric;
    }

    /**
     * Gets current XAUser
     * @return PlatformClient\Controller\User\XAUser
     */
    public function getUser() : PlatformClient\Controller\User\XAUser
    {
        return $this->xaUserCurrent;
    }

}

?>