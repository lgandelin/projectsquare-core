<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateTaskRequest extends Request
{
    public $taskID;
    public $title;
    public $statusID;
    public $description;
    public $estimatedTimeDays;
    public $estimatedTimeHours;
    public $spentTimeDays;
    public $spentTimeHours;
    public $projectID;
    public $order;
    public $allocatedUserID;
    public $requesterUserID;
}