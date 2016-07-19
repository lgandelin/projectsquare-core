<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateTaskRequest extends Request
{
    public $taskID;
    public $title;
    public $status;
    public $projectID;
    public $startDate;
    public $endDate;
}