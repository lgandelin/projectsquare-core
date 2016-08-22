<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class GetTasksRequest extends Request
{
    public $projectID;
    public $statusID;
    public $allocatedUserID;
    public $entities;
}