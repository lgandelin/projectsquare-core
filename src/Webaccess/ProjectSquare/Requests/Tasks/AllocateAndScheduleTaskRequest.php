<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class AllocateAndScheduleTaskRequest extends Request
{
    public $userID;
    public $taskID;
    public $day;
    public $requesterUserID;
}
