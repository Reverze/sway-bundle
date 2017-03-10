<?php

namespace SwayBundle\User;

use SwayBundle\User\Exception;

class User
{
    /**
     * SWUse's object (sway runtime)
     * @var \SWUser
     */
    private $userObject = null;
    
    private $releaseCookiesOnSignin = false;
    
    /**
     * Wrapper for SWUser class. Its working on 'SW_USER' global variables
     * @throws \SWAuthBundle\User\Exception\InitializeException
     */
    public function __construct(bool $releaseCookiesOnSignin = false, \SWUser $externalUserObject = null)
    {
        $this->releaseCookiesOnSignin = (bool) $releaseCookiesOnSignin;
        
        /**
         * If external user's object is defined
         */
        if (!empty($externalUserObject)){
            $this->userObject = $externalUserObject;
        }
        
        if (class_exists("SWUser") && empty($this->userObject)){
            $this->initializeUserObject();
        }
        else if (!class_exists("SWUser")){
            throw new Exception\InitializeException();
        }     
    }
    
    /**
     * Initialize from global variable SW_USER derived by swayengine
     * @global \SWUser $SW_USER
     * @throws \SWAuthBundle\User\Exception\InstanceException
     */
    private function initializeUserObject()
    {
        global $SW_USER;
        
        if ($SW_USER instanceof \SWUser){
            $this->userObject = $SW_USER;
        }
        else{
            throw new Exception\InstanceException();
        }
    }
    
    /**
     * Determines if user is currently online.
     * @return bool
     */
    public function isOnline()
    {
        return (bool) $this->userObject->IsLogged();
    }
    
    /**
     * Ends user session.
     * Returns false on failure
     * @return bool
     */
    public function signoff()
    {
        return (bool) $this->userObject->Logout();
    }
    
    /**
     * Gets user's ID
     * @return integer
     */
    public function getUserID()
    {
        return (int) $this->userObject->current_id;
    }
    
    /**
     * Gets user nickname
     * @return string
     */
    public function getUserName()
    {
        return (string) $this->userObject->current_nick;
    }
    
    /**
     * Gets assigned email address to user's account
     * @return string
     */
    public function getEmailAddress()
    {
        return (string) $this->userObject->current_email;
    }
    
    /**
     * Gets hashed form of user's password
     * @return string
     */
    public function getPasswordHash()
    {
        return (string) $this->userObject->current_password;
    }
    
    /**
     * Gets password's salt
     * @return string
     */
    public function getPasswordSalt()
    {
        return (string) $this->userObject->current_salt;
    }
    
    /**
     * Gets primary user's group' ID
     * @return integer
     */
    public function getGroupID()
    {
        return (int) $this->userObject->current_user_group;
    }
    
    /**
     * Gets assigned flag to user's account (user and group)
     * @return array
     */
    public function getAssignedFlags()
    {
        return (array) $this->userObject->current_flags;   
    }
    
    /**
     * Gets register time
     * @param string $format
     * @return int/string
     */
    public function getRegistertime(string $format = null)
    {
        if (!empty($format)){
            return (string) date($format, $this->userObject->register_time);
        }
        else{
            return (int) $this->userObject->register_time;
        }
    }
    
    /**
     * Gets register ip address
     * @return string
     */
    public function getRegisterIpAddress()
    {
        return (string) $this->userObject->register_ip_addr;
    }
    
    /**
     * Returns avatar URL as string.
     * Returns NULL if not defined
     * @return string
     */
    public function getAvatarUri()
    {
        return (string) $this->userObject->avatarUri;
    }
    
    /**
     * Checks if user's account is confirmed
     * @return boolean
     */
    public function isConfirmed()
    {
        return (bool) $this->userObject->confirmed;
    }
    
    /**
     * Checks if multi-session is enabled for user
     * @return boolean
     */
    public function isMultiSessionEnabled()
    {
        return (bool) $this->userObject->isMultiSessionEnabled();
    }
    
    /**
     * Create a new user account
     * @param string $nickname User's nickname
     * @param string $emailaddress Email address which will be assigned into account
     * @param type $rawpassword Raw user's password
     * @throws \SWAuthBundle\User\Exception\NameReservedException
     * @throws \SWAuthBundle\User\Exception\EmailReservedException
     * @throws \SWAuthBundle\User\Exception\ClientAuthenticationException
     * @throws \SWAuthBundle\User\Exception\PasswordHashException
     * @throws \SWAuthBundle\User\Exception\CreateException
     */
    public function signup(string $nickname, string $emailaddress, $rawpassword)
    {
        $signupStatus = $this->userObject->Register($nickname, $rawpassword, $emailaddress);
        
        switch ($signupStatus){
            case 2:
                throw new Exception\NameReservedException($nickname);
                break;
            case 3:
                throw new Exception\EmailReservedException($emailaddress);
                break;
            case 4:
                throw new Exception\ClientAuthenticationException();
                break;
            case 5:
                throw new Exception\PasswordHashException();
                break;
            case true:
                break;
            default:
                throw new Exception\CreateException();
                break;      
        }   
    }
    
    /**
     * Sign in into user's account
     * @param string $useridentifier
     * @param \SwayBundle\User\strign $rawpassword
     * @throws Exception\ExistsException
     * @throws Exception\InvalidPasswordException
     * @throws Exception\SigninException
     */
    public function signin(string $useridentifier, string $rawpassword)
    {
        $signinStatus = $this->userObject->Login($useridentifier, $rawpassword, $this->releaseCookiesOnSignin);
        
        if ($signinStatus === true){
            return true;
        }
        
        else if ($signinStatus === 2 || $signinStatus === 6 || $signinStatus === 7){
            throw new Exception\ExistsException($useridentifier);
        }
        else if ($signinStatus === 3){
            throw new Exception\InvalidPasswordException();
        }    
        else{
            throw new Exception\SigninException();
        }
       
        
        
    }
    
    /**
     * Sets user's name
     * Returns true on successfull change, returns false on failure
     * @param string $userName
     * @return bool
     */
    public function setUserName(string $userName)
    {
        return \SWUserStatement::ChangeUserNick($this->userObject->current_id, $userName); 
    }
    
    /**
     * Sets user's email address
     * Returns true on successfull change, returns false on failure
     * @param string $emailAddress
     * @return bool
     */
    public function setEmailAddress(string $emailAddress)
    {
        return \SWUserStatement::ChangeUserEmail($this->userObject->current_id, $emailAddress);
    }
    
    /**
     * Sets user's primary group by group's ID
     * Returns true on successfull change, returns false on failure
     * @param int $groupID
     * @return bool
     */
    public function setGroupID(int $groupID)
    {
        return \SWUserStatement::ChangeUserGroup($this->userObject->current_id, $groupID);
    }
    
    /**
     * Sets user's avatar url
     * @param string $avatarUrl
     * @return bool
     */
    public function setAvatarUrl(string $avatarUrl = null)
    {
        return \SWUserStatement::setAvatarUri($this->userObject->current_id, $avatarUrl);
    }
    
    /**
     * Sets multi-session 'enabled' state for user
     * @param bool $multiSessionEnabledState
     * @return boolean
     */
    public function setMultiSessionEnabledState(bool $multiSessionEnabledState = false)
    {
        return $this->userObject->setMultiSessionEnabledState($multiSessionEnabledState);
    }
    
    /**
     * Checks if user's name is free
     * @param string $userName
     * @return boolean
     */
    public function isFreeUserName(string $userName)
    {
        return !(bool) \SWUserStatement::GetUserIDByNick($userName);
    }
    
    /**
     * Checks if user's name is valid
     * @param string $userName
     * @return boolean
     */
    public function validateUserName(string $userName)
    {
        return (bool) preg_match('/^[a-zA-Z0-9\\sążźćęśółńĄŻŹŚĆĘÓŃŁ\\[\\]]+$/', $userName);
    }
    
    
    public function verifyPassword(string $password)
    {
        return \SWUserStatement::CheckPassword($password . $this->userObject->current_salt, $this->userObject->current_password);
    }
    
    /**
     * Creates a new service for another user (not currently logged)
     * @param int $userID
     * @return \SwayBundle\User\User
     * @throws \SwayBundle\User\Exception\UserException
     */
    public function createServiceForUser(int $userID) : User
    {
        /**
         * Initializes user's object with enabled manual mode
         */
        $userObject = new \SWUser(true);
        /**
         * Initializes object for specified user
         */
        $createObjectResult = $userObject->createFor($userID);
        
        /**
         * If object has been initialized successfully
         */
        if ($createObjectResult === true){
            /**
             * Creates user's service using user's object
             */
            $userService = new User(false, $userObject);
            
            return $userService;
        }
        /**
         * When fails, throws an exception
         */
        else{
            throw Exception\UserException::userObjectCreateFailed($userID);
        }
        
    }
    
    /**
     * Validates confirm code
     * @param string $confirmCode
     * @return bool True if valid, False if invalid
     */
    public function validateConfirmCode(string $confirmCode) : bool
    {
        return \SWUserStatement::validateConfirmCode($this->getUserID(), $confirmCode);
    }
    
    /**
     * Sets account an confirmed
     * @return bool True on success, False on failure
     */
    public function confirmAccount() : bool
    {
        return \SWUserStatement::setUserAsConfirmed($this->getUserID());
    }
    
    /**
     * Sets acount an unconfirmed
     * @return bool True on success, False on failure
     */
    public function unconfirmAccount() : bool
    {
        return \SWUserStatement::setUserAsNotconfirmed($this->getUserID());
    }
    
    /**
     * Generated and assigns confirm code
     * @return array
     */
    public function generateNewConfirmCode() : array
    {
        /**
         * Newly generated confirm code for user
         */
        $generatedConfirmCode = \SWUserStatement::generateConfirmCode();
        
        /**
         * Result of assign confirm code to user
         */
        $actionResult = null;
        
        if (\SWUserStatement::isUserHasConfirmCode($this->getUserID())) {
            $actionResult = \SWUserStatement::changeConfirmCode($this->getUserID(), $generatedConfirmCode);
        } 
        else {
            $actionResult = \SWUserStatement::createConfirmCodeTask($this->getUserID(), $generatedConfirmCode);
        }
        
        return [
            'confirmCode' => $generatedConfirmCode,
            'result' => $actionResult
        ];
    }
    
    
    
    
    
}



?>

