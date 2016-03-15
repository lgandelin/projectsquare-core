<?php

namespace Webaccess\ProjectSquare\Repositories;

interface UserRepository
{
    public function getUser($userID);

    public function getUsers();

    public function getAgencyUsers();

    public function getUsersByProject($projectID);

    public function getUsersPaginatedList($limit);

    public function createUser($firstName, $lastName, $email, $password, $clientID);

    public function updateUser($userID, $firstName, $lastName, $email, $password, $clientID);

    public function deleteUser($userID);

    public function getUnreadMessages($userID);

    public function setReadFlagMessage($userID, $messageID, $read);
}
