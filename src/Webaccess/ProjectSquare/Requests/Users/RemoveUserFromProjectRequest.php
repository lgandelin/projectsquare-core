<?php

namespace Webaccess\ProjectSquare\Requests\Users;

use Webaccess\ProjectSquare\Requests\Request;

class RemoveUserFromProjectRequest extends Request
{
    public $userID;
    public $projectID;
    public $requesterUserID;
}