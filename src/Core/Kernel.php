<?php

namespace SwayBundle\Core;

use SwayBundle\Provider\Connector;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Config\Exception\FileLoaderLoadException;

use SwayBundle\Provider\Exception\ProviderException;


class Kernel
{
    /**
     * Platform provider hostname
     * @var string
     */
    private $platformProviderHostName = null;

    /**
     * Platform provider port
     * @var integer
     */
    private $platformProviderPort = null;

    /**
     * Platform application key
     * @var string
     */
    private $platformApplicationKey = null;

    /**
     * Platform client cache driver
     * @var string
     */
    private $cacheDriver = null;

    /**
     * Platform client -> cache lifetime multiplier
     * @var integer
     */
    private $cacheLifetimeMultiplier = 1;

    /**
     * Optional parameters for cache driver
     * @var array
     */
    private $cacheDriverParameters = array();

    /**
     * @var \SwayBundle\Provider\Connector
     */
    private $platformConnectionHandler = null;
    
    public function __construct(string $platformProvider, string $platformAppKey, string $cacheDriver,
                                int $cacheLifetimeMultiplier, array $cacheParameters = array())
    {
        /**
         * Given value has format: "providerhostname:providerport";
         */
        $exploded = explode(":", $platformProvider);

        /**
         * If provider host and port are not passed
         */
        if (!sizeof($exploded)){
            throw ProviderException::emptyPlatformProvider();
        }

        $providerHostname = $exploded[0];
        $providerPort = $exploded[1] ?? null;

        /**
         * If provider hostname is empty
         */
        if (!strlen($providerHostname)){
            throw ProviderException::emptyPlatformProvider();
        }

        /**
         * If provider port is not specified
         */
        if (empty($providerPort)){
            throw ProviderException::emptyProviderPort();
        }

        if (!is_numeric($providerPort)){
            throw ProviderException::invalidProviderPort();
        }

        $providerPort = intval($providerPort);

        if ($providerPort < 0 && $providerPort >= 65535){
            throw ProviderException::invalidProviderPort();
        }

        $this->platformProviderHostName = $providerHostname;
        $this->platformProviderPort = $providerPort;
        $this->cacheDriverParameters = $cacheParameters;

        if (empty($platformAppKey)){
            throw ProviderException::emptyPlatformAppKey();
        }

        $this->platformApplicationKey = $platformAppKey;

        $this->cacheDriver = $cacheDriver;

        $this->cacheLifetimeMultiplier = $cacheLifetimeMultiplier;

        $this->connect();
    }
    
    /**
     * Initialize runtime environment, sets application's properties and launch runtime
     * @throws \SwayBundle\Core\Exception
     */
    public function connect()
    {
        $providerConnector = new Connector([
            'hostname' => $this->platformProviderHostName,
            'port' => $this->platformProviderPort,
            'appkey' => $this->platformApplicationKey,
            'cachedriver' => $this->cacheDriver,
            'lifetimemultiplier' => $this->cacheLifetimeMultiplier,
            'driverparameters' => $this->cacheDriverParameters
        ]);

        $providerConnector->connect();

        $this->platformConnectionHandler = $providerConnector;
    }

    /**
     * Gets platform connection handler
     * @return Connector
     */
    public function getPlatformHandler() : Connector
    {
        return $this->platformConnectionHandler;
    }

    
    
    
}


?>