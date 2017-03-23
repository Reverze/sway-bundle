<?php

namespace SwayBundle\User;

use SwayBundle\Core\Kernel;
use SwayBundle\User\Exception;
use XA\PlatformClient\Controller\User\Exception\UserException;
use XA\PlatformClient\Controller\User\XAUser;
use XA\PlatformClient\Controller\User\XAUserGeneric;
use XA\PlatformClient\Enum\WebService;

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
     * Sends an email message with authorize code to change user name.
     * @param string $userName
     * @return bool|int
     * @throws Exception\AccountNotConfirmedException
     * @throws Exception\InvalidUserNameException
     */
    public function beginUserNameChange(string $userName)
    {
        $beginResult = $this->userObject->beginUsernameChange($userName);

        if ($beginResult === XAUser::THE_SAME_NAME || $beginResult === XAUserGeneric::INVALID_USER_ID
            || $beginResult === XAUserGeneric::EMPTY_USERNAME || $beginResult === XAUserGeneric::UNEXPECTED_ERROR){
            return false;
        }

        if ($beginResult === XAUserGeneric::ACCOUNT_NOT_CONFIRMED_DENIED){
            throw new Exception\AccountNotConfirmedException();
        }

        if ($beginResult === XAUserGeneric::INVALID_USER_NAME){
            throw new Exception\InvalidUserNameException();
        }

        return $beginResult;
    }

    /**
     * Finishes user name change.
     * @param string $authorizeCode
     * @return bool
     * @throws Exception\InvalidAuthorizeCodeException
     */
    public function finishUserNameChange(string $authorizeCode)
    {
        $finishResult = $this->userObject->finishUsernameChange($authorizeCode);

        if ($finishResult === XAUserGeneric::UNEXPECTED_ERROR || $finishResult === XAUserGeneric::USER_NOT_FOUND
            || $finishResult === XAUserGeneric::INVALID_USER_ID){
            return false;
        }

        if ($finishResult === XAUserGeneric::INVALID_AUTHORIZE_CODE){
            throw new Exception\InvalidAuthorizeCodeException();
        }

        return (bool) $finishResult;
    }

    /**
     * Sends en email with authorize code to change user's password.
     * @param string $newPassword
     * @return bool
     * @throws Exception\AccountNotConfirmedException
     * @throws Exception\InvalidPasswordException
     */
    public function beginPasswordChange(string $newPassword)
    {
        $beginResult = $this->userObject->beginPasswordChange($newPassword);

        if ($beginResult === XAUserGeneric::EMPTY_USER_PASSWORD || $beginResult === XAUserGeneric::UNEXPECTED_ERROR ||
            $beginResult === XAUserGeneric::INVALID_USER_ID){
            return false;
        }

        if ($beginResult === XAUserGeneric::ACCOUNT_NOT_CONFIRMED_DENIED){
            throw new Exception\AccountNotConfirmedException();
        }

        if ($beginResult === XAUserGeneric::INVALID_USER_PASSWORD){
            throw new Exception\InvalidPasswordException();
        }

        return (bool) $beginResult;
    }

    /**
     * Finishes password change
     * @param string $authorizeCode
     * @return bool
     * @throws Exception\InvalidAuthorizeCodeException
     */
    public function finishPasswordChange(string $authorizeCode)
    {
        $finishResult = $this->userObject->finishPasswordChange($authorizeCode);

        if ($finishResult === XAUserGeneric::INVALID_USER_ID || $finishResult === XAUserGeneric::UNEXPECTED_ERROR ||
            $finishResult === XAUserGeneric::USER_NOT_FOUND){
            return false;
        }

        if ($finishResult === XAUserGeneric::INVALID_AUTHORIZE_CODE){
            throw new Exception\InvalidAuthorizeCodeException();
        }

        return (bool) $finishResult;
    }

    /**
     * Creates an ask to change email address. Email message will be sent to current user mailbox,
     * with authorize code which will be used to confirm ask.
     * @param string $newEmailAddress
     * @return bool
     * @throws Exception\AccountNotConfirmedException
     * @throws Exception\InvalidEmailAddressException
     */
    public function createAskForChangeEmailAddress(string $newEmailAddress)
    {
        $askResult = $this->userObject->createAskEmailAddressChange($newEmailAddress);

        if ($askResult === XAUserGeneric::INVALID_USER_ID || $askResult === XAUserGeneric::UNEXPECTED_ERROR ||
            $askResult === XAUserGeneric::EMPTY_EMAIL_ADDRESS){
            return false;
        }

        if ($askResult === XAUserGeneric::ACCOUNT_NOT_CONFIRMED_DENIED){
            throw new Exception\AccountNotConfirmedException();
        }

        if ($askResult === XAUserGeneric::INVALID_EMAIL_ADDRESS){
            throw new Exception\InvalidEmailAddressException();
        }

        return (bool) $askResult;
    }

    /**
     * Acepts ask for change email address. After that, next email message will be send to new user mailbox
     * with authorize code to confirm an new email address.
     * @param string $authorizeCode
     * @return bool
     * @throws Exception\AccountNotConfirmedException
     * @throws Exception\InvalidAuthorizeCodeException
     */
    public function acceptAskForChangeEmailAddress(string $authorizeCode)
    {
        $acceptResult = $this->userObject->acceptAskEmailAddressChange($authorizeCode);

        if ($acceptResult === XAUserGeneric::INVALID_USER_ID || $acceptResult === XAUserGeneric::UNEXPECTED_ERROR ||
            $acceptResult === XAUserGeneric::USER_NOT_FOUND){
            return false;
        }

        if ($acceptResult === XAUserGeneric::ACCOUNT_NOT_CONFIRMED_DENIED){
            throw new Exception\AccountNotConfirmedException();
        }

        if ($acceptResult === XAUserGeneric::INVALID_AUTHORIZE_CODE){
            throw new Exception\InvalidAuthorizeCodeException();
        }

        return (bool) $acceptResult;
    }

    /**
     * Finishes email address change action.
     * @param string $authorizeCode
     * @return bool
     * @throws Exception\AccountNotConfirmedException
     * @throws Exception\InvalidAuthorizeCodeException
     */
    public function finishChangeEmailAddress(string $authorizeCode)
    {
        $finishResult = $this->userObject->finishEmailAddressChange($authorizeCode);

        if ($finishResult === XAUserGeneric::INVALID_USER_ID || $finishResult === XAUserGeneric::UNEXPECTED_ERROR ||
            $finishResult === XAUserGeneric::USER_NOT_FOUND){
            return false;
        }

        if ($finishResult === XAUserGeneric::ACCOUNT_NOT_CONFIRMED_DENIED){
            throw new Exception\AccountNotConfirmedException();
        }

        if ($finishResult === XAUserGeneric::INVALID_AUTHORIZE_CODE){
            throw new Exception\InvalidAuthorizeCodeException();
        }

        return (bool) $finishResult;
    }


    /**
     * Changes user avatar
     * @param string $avatarUrl
     * @return bool
     * @throws Exception\AccountNotConfirmedException
     * @throws Exception\ResourceNotAvailableException
     * @throws Exception\ResourceNotFoundException
     * @throws Exception\WebServiceNotAvailableException
     */
    public function changeUserAvatar(string $avatarUrl)
    {
        $updateResult = $this->userObject->changeAvatar($avatarUrl);

        if ($updateResult === XAUserGeneric::INVALID_USER_ID || $updateResult === XAUserGeneric::UNEXPECTED_ERROR ||
            $updateResult === XAUserGeneric::USER_NOT_FOUND){
            return false;
        }

        if ($updateResult === XAUserGeneric::ACCOUNT_NOT_CONFIRMED_DENIED){
            throw new Exception\AccountNotConfirmedException();
        }

        if ($updateResult === WebService::NOT_AVAILABLE){
            throw new Exception\WebServiceNotAvailableException();
        }

        if ($updateResult === WebService::RESOURCE_NOT_FOUND){
            throw new Exception\ResourceNotFoundException();
        }

        if ($updateResult === WebService::RESOURCE_NOT_AVAILABLE){
            throw new Exception\ResourceNotAvailableException();
        }

        return (bool) $updateResult;//true
    }

    /**
     * Drops user avatar
     * @return bool
     */
    public function dropUserAvatar()
    {
        $dropResult = $this->userObject->dropAvatar();

        if ($dropResult === XAUserGeneric::INVALID_USER_ID || $dropResult === XAUserGeneric::UNEXPECTED_ERROR){
            return false;
        }

        return (bool) $dropResult;//true
    }


    
    /**
     * Checks if user's name is free
     * @param string $userName
     * @return boolean
     */
    public function isFreeUserName(string $userName)
    {
        return $this->userObject->getGeneric()->isUserNameAvailable($userName);
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

    /**
     * Verifies user password
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password)
    {
        $verifyResult = $this->userObject->verifyUserPassword($password);

        if ($verifyResult === XAUserGeneric::UNEXPECTED_ERROR){
            return false;
        }

        return (bool) $verifyResult;//true or false
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
     * Confirms user account
     * @param string $confirmCode
     * @return bool
     * @throws Exception\AccountAlreadyConfirmedException
     */
    public function confirmAccount(string $confirmCode) : bool
    {
        $confirmResult = null;
        try {
            $confirmResult = $this->userObject->confirmAccount($confirmCode);

            if ($confirmResult === XAUserGeneric::INVALID_USER_ID || $confirmResult === XAUserGeneric::EMPTY_CONFIRM_CODE){
                return false;
            }

            if ($confirmResult === XAUserGeneric::ACCOUNT_ALREADY_CONFIRMED){
                throw new Exception\AccountAlreadyConfirmedException();
            }

            return (bool) $confirmResult; //true or false
        }
        catch(UserException $exception){
            throw new Exception\AccountAlreadyConfirmedException();
        }
    }

    /**
     * Resend confirm code
     * @param null $newEmailAddress
     * @return bool
     * @throws Exception\AccountAlreadyConfirmedException
     */
    public function resendConfirmCode($newEmailAddress = null)
    {
        $resendResult = $this->userObject->resendConfirmCode($newEmailAddress);

        if ($resendResult === XAUserGeneric::INVALID_USER_ID ||
            $resendResult === XAUserGeneric::UNEXPECTED_ERROR){
            return false;
        }

        if ($resendResult === XAUserGeneric::ACCOUNT_ALREADY_CONFIRMED){
            throw new Exception\AccountAlreadyConfirmedException();
        }

        return (bool) $resendResult;
    }


    /**
     * Reminds user's name
     * @param string $emailAddress
     * @return bool
     * @throws Exception\UserNotFoundException
     */
    public function remindUsername(string $emailAddress)
    {
        $remindResult = $this->userObject->remindUsername($emailAddress);

        if ($remindResult === XAUserGeneric::USER_NOT_FOUND){
            throw new Exception\UserNotFoundException();
        }

        if ($remindResult === XAUserGeneric::UNEXPECTED_ERROR){
            return false;
        }

        return (bool) $remindResult;
    }

    /**
     * Reminds user's password
     * @param string $emailAddress
     * @return bool
     * @throws Exception\UserNotFoundException
     */
    public function remindPassword(string $emailAddress)
    {
        $remindResult = $this->userObject->remindPassword($emailAddress);

        if ($remindResult === XAUserGeneric::USER_NOT_FOUND){
            throw new Exception\UserNotFoundException();
        }

        if ($remindResult === XAUserGeneric::UNEXPECTED_ERROR){
            return false;
        }

        return (bool) $remindResult;
    }
    
    
    
}



?>

