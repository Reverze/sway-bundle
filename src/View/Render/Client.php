<?php

namespace SwayBundle\View\Render;


/**
 * Wrapper for SWTemplates package
 */
class Client
{
    /**
     * Additional configuration file path.
     * Additional configuration for SWTemplates.
     * Its optional.
     * 
     * For default, set as null
     * @var string
     */
    private $additionalConfigurationFilePath = null;
    
    /**
     * SWTemplates object (render)
     * @var \SWTemplates
     */
    private $templateRenderObject = null;
    
    /**
     * 
     * @param string $additionalConfigurationFilePath
     */
    public function __construct(string $additionalConfigurationFilePath = null)
    {
        /**
         * Stores additional configuration file path
         */
        $this->additionalConfigurationFilePath = $additionalConfigurationFilePath;
        
        /**
         * Creates a new render object
         */
        $this->templateRenderObject = new \SWTemplates($this->additionalConfigurationFilePath);
    }
    
    /**
     * Assigns variable into template
     * @param string $variableName Variable's name
     * @param mixed $value Variable's value
     * @return \SwayBundle\View\Render\Client
     */
    public function assign(string $variableName, $value)
    {
        /**
         * Assigns variable into template
         */
        $this->templateRenderObject->assign($variableName, $value);
        
        return $this;
    }
    
    /**
     * Renders template and view it
     * @param string $callPathStringify
     * @return \SwayBundle\View\Render\Client
     */
    public function render(string $callPathStringify)
    {
        /**
         * Renders template and view it
         */
        $this->templateRenderObject->render($callPathStringify);
        
        return $this;
    }
    
    /**
     * Gets and render template
     * @param string $callPathStringify
     * @return string
     */
    public function get(string $callPathStringify)
    {
        return $this->templateRenderObject->get($callPathStringify);
    }
    
    
}


?>

