<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class CreateTaskRequest extends Request
{
    public $title;
    public $statusID;
    public $description;
    public $estimatedTimeDays;
    public $estimatedTimeHours;
    public $spentTimeDays;
    public $spentTimeHours;
    public $projectID;
    public $startDate;
    public $endDate;
    public $allocatedUserID;
}