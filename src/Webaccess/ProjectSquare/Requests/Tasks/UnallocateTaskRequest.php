<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class UnallocateTaskRequest extends Request
{
    public $taskID;
    public $requesterUserID;
}