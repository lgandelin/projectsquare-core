<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateTaskRequest extends Request
{
    public $taskID;
    public $title;
    public $statusID;
    public $description;
    public $estimatedTime;
    public $projectID;
    public $startDate;
    public $endDate;
    public $allocatedUserID;
}