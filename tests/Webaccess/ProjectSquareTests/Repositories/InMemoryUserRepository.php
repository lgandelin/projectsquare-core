<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\User;
use Webaccess\ProjectSquare\Repositories\UserRepository;

class InMemoryUserRepository implements UserRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    public function getNextID()
    {
        return count($this->objects) + 1;
    }

    public function getUser($userID)
    {
        if (isset($this->objects[$userID])) {
            return $this->objects[$userID];
        }

        return false;
    }

    public function getUsers()
    {
        // TODO: Implement getUsers() method.
    }

    public function getAgencyUsers()
    {
        return [];
    }

    public function getUsersByProject($projectID)
    {
        $result = [];

        foreach ($this->objects as $user) {
            if (in_array($projectID, $user->projects)) {
                $result[]= $user;
            }
        }

        return $result;
    }

    public function getUsersPaginatedList($limit)
    {
        // TODO: Implement getUsersPaginatedList() method.
    }

    public function createUser($firstName, $lastName, $email, $password, $phone, $mobile, $clientID, $clientRole, $roleID, $isAdministrator=false)
    {
        // TODO: Implement createUser() method.
    }

    public function updateUser($userID, $firstName, $lastName, $email, $password, $phone, $mobile, $clientID, $clientRole, $roleID, $isAdministrator=false)
    {
        // TODO: Implement updateUser() method.
    }

    public function deleteUser($userID)
    {
        // TODO: Implement deleteUser() method.
    }

    public function persistUser(User $user)
    {
        if (!isset($user->id)) {
            $user->id = self::getNextID();
        }
        $this->objects[$user->id]= $user;

        if (!isset($this->objects[$user->id]->unread_messages)) {
            $this->objects[$user->id]->unread_messages = [];
        }
        $user->projects = [];

        return $user;
    }

    public function getUnreadMessages($userID)
    {
        $result = [];
        foreach ($this->objects[$userID]->unread_messages as $messageID => $messageRead) {
            if (!$messageRead) {
                $result[]= $messageID;
            }
        }

        return $result;
    }

    public function setReadFlagMessage($userID, $messageID, $read)
    {
        $this->objects[$userID]->unread_messages[$messageID] = $read;
    }

    public function getClientUsers($clientID)
    {
        return [];
    }

    public function getAgencyUsersPaginatedList($limit, $sortColumn = null, $sortOrder = null)
    {
        // TODO: Implement getAgencyUsersPaginatedList() method.
    }

    public function getUserByEmail($userEmail)
    {
        foreach ($this->objects as $user) {
            if ($user->email == $userEmail) {
                return $user;
            }
        }

        return false;
    }

    public function getUsersByRole($roleID)
    {
        // TODO: Implement getUsersByRole() method.
    }
}