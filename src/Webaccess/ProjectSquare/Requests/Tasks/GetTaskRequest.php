<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class GetTaskRequest extends Request
{
    public $taskID;
    public $requesterUserID;
}