<?php

namespace SwayBundle\Core;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Config\Exception\FileLoaderLoadException;

use SwayBundle\Application\Exception\WorkingDirectoryException;
use SwayBundle\Application\Exception\ApplicationIdentifierEmptyException;

class Kernel
{
    /**
     * Sway framework path
     * @var string
     */
    private $runtimePath = null;
    
    /**
     * Base script filename
     * @var string
     */
    private $binFile = null;
    
    /**
     * Load mode
     * @var string
     */
    private $loadMode = null;
    
    /**
     * Application's identifier
     * @var string
     */
    private $appIdentifier = null;
    
    /**
     * Application's working directory path
     * @var string
     */
    private $workingDirectory = null;
    
    /**
     * Determien if cross domains session is enabled
     * @var boolean 
     */
    private $crossDomainsSessionEnabled = false;
    
    public function __construct(string $runtimePath, string $binFile, string $loadMode, string $appIdentifier, string $workingDirectory, bool $crossDomainSessionEnabled)
    {
        /* Sets runtime's path */
        $this->runtimePath = (string) $runtimePath;
        /* Sets filename on sway framework base script */
        $this->binFile = (string) $binFile;
        /* Determine in which mode kernel gonna to boot */
        $this->loadMode = (string) $loadMode;
        /* Application's identifier */
        $this->appIdentifier = (string) $appIdentifier;
        /* Application's working directory */
        $this->workingDirectory = (string) $workingDirectory; 
        $this->crossDomainsSessionEnabled = (bool) $crossDomainSessionEnabled;
         
    }
    
    /**
     * Initialize runtime environment, sets application's properties and launch runtime
     * @throws \SwayBundle\Core\Exception
     */
    public function initialize()
    {
        try {
            if ($this->initializeRuntimeEnvironment()){
                $this->initializeApplication();
            }
        } 
        catch (Exception $ex) {
            throw $ex;
        }
        
    }
    
    /**
     * Initialize runtime environment
     * @return boolean
     * @throws FileLoaderLoadException
     * @throws FileNotFoundException
     */
    private function initializeRuntimeEnvironment()
    {
        $runtimeFileAbsolutePath = $this->runtimePath . DIRECTORY_SEPARATOR . $this->binFile;
        
        if (is_file($runtimeFileAbsolutePath)){
            
            if (!@require_once($runtimeFileAbsolutePath)){
                throw new FileLoaderLoadException ("Cannot include file '$runtimeFileAbsolutePath'");
            }
            else{
                return true;
            }
           
        }
        else{
            throw new FileNotFoundException ("File '$runtimeFileAbsolutePath' not found!");   
        }
        
        return false;
        
    }
    
    /**
     * Sets application's properties and launch runtime environment
     * @throws WorkingDirectoryException
     * @throws ApplicationIdentifierEmptyException
     */
    private function initializeApplication()
    {
        if (!is_dir($this->workingDirectory)){
            throw new WorkingDirectoryException ($this->workingDirectory);
        }
        
        \SwayEngineBoot::$working_directory = (string) $this->workingDirectory;
        
        switch (strtolower($this->loadMode)){
            case "default":
                \SwayEngineBoot::$load_mode = \SwayEngineLoadModeCollection::DEFAULT_MODE;
                break;
            case 'cron':
                \SwayEngineBoot::$load_mode = \SwayEngineLoadModeCollection::CRON_MODE;
                break;
            case 'lite':
                \SwayEngineBoot::$load_mode = \SwayEngineLoadModeCollection::LITE_MODE;
                break;
            case 'class':
                \SwayEngineBoot::$load_mode = \SwayEngineLoadModeCollection::CLASS_MODE;
                break;
            case 'console':
                \SwayEngineBoot::$load_mode = \SwayEngineLoadModeCollection::CONSOLE_MODE;
                break;
            case 'utils':
                \SwayEngineBoot::$load_mode = \SwayEngineLoadModeCollection::UTILS_MODE;
                break;
            default:
                \SwayEngineBoot::$load_mode = \SwayEngineLoadModeCollection::DEFAULT_MODE;
                break;
        }
        
        if (empty($this->appIdentifier) || !strlen($this->appIdentifier)){
            throw new ApplicationIdentifierEmptyException();
        }
        else{
            \SwayEngineBoot::$ApplicationIdentifier = $this->appIdentifier;
        }
        
        \SwayEngineBoot::Boot();
        
        if ($this->crossDomainsSessionEnabled){
            \SWMultiDomainsSession::Start();
        }
        
    }
    
    
    
    
}


?>