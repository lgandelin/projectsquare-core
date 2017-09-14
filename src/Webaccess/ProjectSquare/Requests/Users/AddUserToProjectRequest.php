<?php

namespace Webaccess\ProjectSquare\Requests\Users;

use Webaccess\ProjectSquare\Requests\Request;

class AddUserToProjectRequest extends Request
{
    public $userID;
    public $projectID;
    public $roleID;
    public $requesterUserID;
}