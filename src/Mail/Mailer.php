<?php

namespace SwayBundle\Mail;

/**
 * Mailer service
 */
class Mailer
{
    /**
     * Default broadcaster's email address
     * @var string
     */
    private $defaultEmailBroadcaster = null;
    
    
    public function __construct(string $defaultEmailBroadcaster)
    {
        if (!empty($defaultEmailBroadcaster)){
            $this->defaultEmailBroadcaster = $defaultEmailBroadcaster;
        }
    }
    
    /**
     * Send email
     * @param string $emailContent
     * @param string $emailSubject
     * @param string $emailReceiver
     * @param string $emailBroadcaster
     * @return bool
     */
    public function send(string $emailContent, string $emailSubject, string $emailReceiver, string $emailBroadcaster = null) : bool
    {
        return (bool) \SWMail::create($emailSubject, 
                $emailReceiver, 
                $emailContent, 
                (empty($emailBroadcaster) ? $this->defaultEmailBroadcaster : $emailBroadcaster));
    }
}


?>

