<?php

namespace SwayBundle\User;

class Password
{
    /**
     * Minimum password's length
     * @var integer
     */
    private $minimumPasswordLength = null;
    
    /**
     * Maximum password's length
     * @var integer
     */
    private $maximumPasswordLength = null;
    
    /**
     * Determines if password must contains digit(s)
     * @var boolean
     */
    private $passwordContainsDigits = null;
    
    /**
     * Determines if password must contains uppercase letters
     * @var boolean
     */
    private $passwordContainsUppercase = null;
    
    public function __construct() 
    {
        /**
         * Initializes parameters
         */
        $this->initializeParameters();
    }
    
    /**
     * Initializes paramters from sw setup
     */
    protected function initializeParameters()
    {
        /**
         * Gets minimum password's length from sw setup
         */
        $this->minimumPasswordLength = \SWSetup::Get('minimum_password_length', 4, \SWType::Integer);
        
        /**
         * Gets maximum password's length from sw setup
         */
        $this->maximumPasswordLength = \SWSetup::Get('maximum_password_length', 20, \SWType::Integer);
        
        /**
         * Gets 
         */
        $this->passwordContainsDigits = \SWSetup::Get('password_contains_digits', false, \SWType::Boolean);
        
        $this->passwordContainsUppercase = \SWSetup::Get('password_contains_uppercase', false, \SWType::Boolean);
    }
    
    /**
     * Checks if minimum length of password is fulfilled
     * @param string $passwordSource
     * @return boolean
     */
    public function isFulfilledMinimumLength(string $passwordSource)
    {
        return (bool) (strlen($passwordSource) >= $this->minimumPasswordLength);
    }
    
    /**
     * Checks if maximum length of password is fulfilled
     * @param string $passwordSource
     * @return boolean
     */
    public function isFulfilledMaximumLength(string $passwordSource)
    {
        return (bool) (strlen($passwordSource) <= $this->maximumPasswordLength);
    }
    
    /**
     * Checks if password contains digit(s) (if needed)
     * @param string $passwordSource
     * @return boolean
     */
    public function isFulfilledContainsDigits(string $passwordSource)
    {
        /**
         * If there is no need password contains digits, return true
         */
        if (!$this->passwordContainsDigits){
            return true;
        }
        else{
            if (preg_match('/[0-9]+/', $passwordSource)){
                return true;
            }
            else{
                return false;
            }
        }
    }
    
    /**
     * Checks if password contains uppercase letter(s) (if needed)
     * @param string $passwordSource
     * @return boolea
     */
    public function isFulfilledContainsUppercase(string $passwordSource)
    {
        if (!$this->passwordContainsUppercase){
            return true;
        }
        else{
            if (strtolower($passwordSource) === $passwordSource){
                return false;
            }
            else{
                return true;
            }
        }
    }
    
    
    /**
     * Gets minimum length of password
     * @return integer
     */
    public function getMinimumLength()
    {
        return (int) $this->minimumPasswordLength;
    }
    
    /**
     * Gets maximum length of password
     * @return integer
     */
    public function getMaximumLength()
    {
        return (int) $this->maximumPasswordLength;
    }
    
    /**
     * Gets if password must contains digits
     * @return boolean
     */
    public function isMustContainsDigits()
    {
        return (bool) $this->passwordContainsDigits;
    }
}


?>
