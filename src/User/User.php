<?php

namespace SwayBundle\User;

use SwayBundle\Core\Kernel;
use SwayBundle\User\Exception;
use XA\PlatformClient\Controller\User\XAUser;

class User
{

    /**
     * @var \XA\PlatformClient\Controller\User\XAUser
     */
    private $userObject = null;

    /**
     * @var \SwayBundle\Core\Kernel
     */
    private $kernel = null;
    

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;

        $this->initializeUserObject();
    }


    /**
     * Initializes user object
     */
    private function initializeUserObject()
    {
        $this->userObject = $this->kernel->getPlatformHandler()->getUser();
    }
    
    /**
     * Determines if user is currently online.
     * @return bool
     */
    public function isOnline()
    {
        return (bool) $this->userObject->isOnline();
    }
    
    /**
     * Ends user session.
     * Returns false on failure
     * @return bool
     */
    public function signoff()
    {
        return (bool) $this->userObject->logout(true);
    }
    
    /**
     * Gets user's ID
     * @return integer
     */
    public function getUserID()
    {
        return (int) $this->userObject->getUserId();
    }
    
    /**
     * Gets user nickname
     * @return string
     */
    public function getUserName()
    {
        return (string) $this->userObject->getUserName();
    }
    
    /**
     * Gets assigned email address to user's account
     * @return string
     */
    public function getEmailAddress()
    {
        return (string) $this->userObject->getEmail();
    }


    /**
     * Gets primary user's group' ID
     * @return string
     */
    public function getGroupID()
    {
        return  $this->userObject->getUserGroupId();
    }

    /**
     * Gets assigned flag to user's account (user and group)
     * @return array
     */
    public function getAssignedFlags()
    {
        return array("z");
    }

    /**
     * Gets register time
     * @param string $format
     * @return int/string
     */
    public function getRegistertime(string $format = null)
    {
        if (!empty($format)){
            return (string) date($format, $this->userObject->getRegisterTime());
        }
        else{
            return (int) $this->userObject->getRegisterTime();
        }
    }
    
    /**
     * Gets register ip address
     * @return string
     */
    public function getRegisterIpAddress()
    {
        return (string) $this->userObject->getRegisterIpAddress();
    }
    
    /**
     * Returns avatar URL as string.
     * Returns NULL if not defined
     * @return string
     */
    public function getAvatarUri()
    {
        return (string) $this->userObject->getAvatarUrl();
    }
    
    /**
     * Checks if user's account is confirmed
     * @return boolean
     */
    public function isConfirmed()
    {
        return (bool) $this->userObject->isAccountConfirmed();
    }


    /**
     * Creates a new user account
     * @param string $nickname
     * @param string $emailaddress
     * @param string $rawpassword
     * @return bool
     * @throws Exception\ConfirmCodeException
     * @throws Exception\ConfirmMailException
     * @throws Exception\CreateException
     * @throws Exception\EmailReservedException
     * @throws Exception\EmptyUserNameException
     * @throws Exception\EmptyUserPasswordException
     * @throws Exception\InvalidEmailAddressException
     * @throws Exception\NameReservedException
     */
    public function signup(string $nickname, string $emailaddress, string $rawpassword)
    {
        $signupStatus = $this->userObject->register($nickname, $emailaddress, $rawpassword);
        
        switch ($signupStatus){
            case XAUser::EMPTY_USERNAME:
                throw new Exception\EmptyUserNameException();
                break;
            case XAUser::EMPTY_USERPASSWORD:
                throw new Exception\EmptyUserPasswordException();
                break;
            case XAUser::INVALID_EMAIL_ADDRESS:
                throw new Exception\InvalidEmailAddressException();
                break;
            case XAUser::RESERVED_USER_NAME:
                throw new Exception\NameReservedException($nickname);
                break;
            case XAUser::RESERVED_EMAIL_ADDRESS:
                throw new Exception\EmailReservedException($emailaddress);
                break;
            case XAUser::PREPARE_CONFIRMATION_FAILED:
                throw new Exception\ConfirmCodeException();
                break;
            case XAUser::MAIL_SEND_FAILED:
                throw new Exception\ConfirmMailException();
                break;
            case true:
                return true;
                break;
            default:
                throw new Exception\CreateException();
                break;      
        }   
    }

    /**
     * Signin to user account
     * @param string $useridentifier
     * @param string $rawpassword
     * @return bool
     * @throws Exception\ExistsException
     * @throws Exception\InvalidPasswordException
     * @throws Exception\SigninException
     */
    public function signin(string $useridentifier, string $rawpassword)
    {
        $signinStatus = $this->userObject->signin($useridentifier, $rawpassword);
        
        if ($signinStatus === XAUser::OK){
            return true;
        }
        
        else if ($signinStatus === XAUser::USER_NOT_FOUND){
            throw new Exception\ExistsException($useridentifier);
        }
        else if ($signinStatus === XAUser::INVALID_PASSWORD){
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

