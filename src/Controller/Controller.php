<?php

namespace SwayBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Response;

use SwayBundle\Mail\Mailer;


class Controller extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
    /**
     *
     * @var \SwayBundle\Core\Kernel
     */
    private $kernelService = null;
    
    /**
     *
     * @var \SwayBundle\User\User
     */
    private $userService = null;
    
    /**
     * Base response object
     * @var \Symfony\Component\HttpFoundation\Response;
     */
    private $baseResponse = null;
    
    
    public function __construct()
    {
            
        
    }
    
    /**
     * This method initializes swaykernel and create swaykernel as service
     * @throws ServiceNotFoundException
     */
    private function initializeKernel()
    {
        /**
         * Gets swaykernel as service
         */
        $this->kernelService = $this->get('swaykernelService');
        
        /**
         * If service not found
         */
        if (empty($this->kernelService)){
            throw new ServiceNotFoundException (
                    sprintf("Service is not defined. Please define service '%s' in app's services", 
                            'swaykernelService')
                    );
        }
        else {
            /**
             * Initialize swaykernel
             */
            $this->kernelService->initialize();
        }
    }
    
    /**
     * Initialize swayUser as service
     * @throws ServiceNotFoundException
     */
    public function initializeUserService()
    {
        /**
         * Gets User object as service
         */
        $this->userService = $this->get('swayuserService');
        
        if (empty($this->kernelService)){
            throw new ServiceNotFoundException (
                sprintf("Service is not defined. Please define service '%s' in app's services",
                        'swayuserService')
                );
        }
    }
    
    /**
     * Creates base response
     */
    public function createBaseResponse()
    {
        $this->baseResponse = new Response();
    }
    
    protected function doSomeStuff()
    {
        /** 
         * Initialize kernel
         */
        $this->initializeKernel();
        
        /**
         * Initialize user service
         */
        $this->initializeUserService();
        
        /**
         * Creates basic response object
         */
        $this->createBaseResponse();
    }
    
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->doSomeStuff();
    }
   
    
    /**
     * Returns basic response object
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getBaseResponse()
    {
        return $this->baseResponse;
    }
    
    public function render($view, array $parameters = array(), Response $response = null) 
    {
        $baseParameters = array();
        
        /**
         * Puts data about current sway user into template
         */
        $baseParameters['swayuser'] = [
            'online' => (bool) $this->getSwayUser()->isOnline(),
            'name' => (string) $this->getSwayUser()->getUserName(),
            'uid' => (int) $this->getSwayUser()->getUserID(),
            'email' => (string) $this->getSwayUser()->getEmailAddress(),
            'gid' => (int) $this->getSwayUser()->getGroupID(),
            'avatarurl' => (string) $this->getSwayUser()->getAvatarUri(),
            'registertime' => (int) $this->getSwayUser()->getRegistertime(),
            'registeripaddress' => (string) $this->getSwayUser()->getRegisterIpAddress(),
            'confirmed' => (bool) $this->getSwayUser()->isConfirmed(),
            'multisessionenabled' => (bool) $this->getSwayUser()->isMultiSessionEnabled()
            
        ];
        
        $baseParameters = array_merge($baseParameters, $parameters);
        
        return parent::render(
                $view, 
                $baseParameters, 
                (empty($response) ? $this->getBaseResponse() : $response));
    }
    
    /**
     * Gets current instance of sway user
     * @return \SwayBundle\User\User
     */
    public function getSwayUser()
    {
        return $this->userService;
    }
    
    /**
     * Gets sway mailer's service
     * @return \SwayBundle\Mail\Mailer
     * @throws \SwayBundle\Controller\Exception\ControllerException
     */
    public function getMailer() : Mailer
    {
        if ($this->has('swayMailer')){
            return $this->get('swayMailer');
        }
        else{
            throw Exception\ControllerException::mailerServiceMissed('swayMailer');
        }
    }
    
    
}

?>