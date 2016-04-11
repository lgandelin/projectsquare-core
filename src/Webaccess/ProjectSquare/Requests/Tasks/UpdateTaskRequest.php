<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateTaskRequest extends Request
{
    public $taskID;
    public $name;
    public $status;
    public $requesterUserID;
}