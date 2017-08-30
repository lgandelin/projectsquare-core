<?php

namespace Webaccess\ProjectSquare\Repositories;

interface UserRepository
{
    public function getUser($userID);

    public function getUserByEmail($userEmail);

    public function getUsers();

    public function getAgencyUsers();

    public function getClientUsers($clientID);

    public function getUsersByProject($projectID);

    public function getUsersByRole($roleID);

    public function getAgencyUsersPaginatedList($limit, $sortColumn = null, $sortOrder = null);

    public function createUser($firstName, $lastName, $email, $password, $phone, $mobile, $clientID, $clientRole, $roleID, $isAdministrator=false);

    public function updateUser($userID, $firstName, $lastName, $email, $password, $phone, $mobile, $clientID, $clientRole, $roleID, $isAdministrator=false);

    public function deleteUser($userID);

    public function getUnreadMessages($userID);

    public function setReadFlagMessage($userID, $messageID, $read);
}
