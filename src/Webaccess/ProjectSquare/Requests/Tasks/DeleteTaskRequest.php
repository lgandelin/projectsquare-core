<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteTaskRequest extends Request
{
    public $taskID;
    public $requesterUserID;
}