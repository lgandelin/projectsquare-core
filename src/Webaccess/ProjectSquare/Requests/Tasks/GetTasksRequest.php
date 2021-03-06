<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class GetTasksRequest extends Request
{
    public $userID;
    public $projectID;
    public $phaseID;
    public $statusID;
    public $allocatedUserID;
    public $entities;
}