<?php

namespace Webaccess\ProjectSquare\Repositories;

interface UserRepository
{
    public function getUser($userID);

    public function getUsers();

    public function getAgencyUsers();

    public function getClientUsers($clientID);

    public function getUsersByProject($projectID);

    public function getAgencyUsersPaginatedList($limit);

    public function createUser($firstName, $lastName, $email, $password, $password, $mobile, $clientID, $clientRole, $isAdministrator=false);

    public function updateUser($userID, $firstName, $lastName, $email, $password, $password, $mobile, $clientID, $clientRole, $isAdministrator=false);

    public function deleteUser($userID);

    public function getUnreadMessages($userID);

    public function setReadFlagMessage($userID, $messageID, $read);
}
