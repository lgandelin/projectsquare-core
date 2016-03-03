<?php

namespace Webaccess\GatewayTests\Repositories;

use Webaccess\Gateway\Entities\User;
use Webaccess\Gateway\Repositories\UserRepository;

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
        // TODO: Implement getAgencyUsers() method.
    }

    public function getUsersPaginatedList($limit)
    {
        // TODO: Implement getUsersPaginatedList() method.
    }

    public function createUser($firstName, $lastName, $email, $password, $clientID)
    {
        // TODO: Implement createUser() method.
    }

    public function updateUser($userID, $firstName, $lastName, $email, $password, $clientID)
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

        return $user;
    }
}