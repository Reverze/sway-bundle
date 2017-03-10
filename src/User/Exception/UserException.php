<?php

namespace SwayBundle\User\Exception;

class UserException extends \Exception
{
    /**
     * Throws an exception when create user's object failed (SWUser)
     * @param int $userID
     * @return \SwayBundle\User\Exception\UserException
     */
    public static function userObjectCreateFailed(int $userID) : UserException
    {
        return (new UserException(sprintf("Create user's object (SwUser) failed for user's ID: '%d'", $userID)));
    }
}

?>

