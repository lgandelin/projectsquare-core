<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class CreateTaskRequest extends Request
{
    public $title;
    public $status;
    public $projectID;
    public $startDate;
    public $endDate;
}