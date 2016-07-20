<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class CreateTaskRequest extends Request
{
    public $title;
    public $statusID;
    public $description;
    public $estimatedTime;
    public $projectID;
    public $startDate;
    public $endDate;
    public $allocatedUserID;
}