<?php

namespace Webaccess\ProjectSquare\Requests\Planning;

use Webaccess\ProjectSquare\Requests\Request;

class AllocateTaskInPlanningRequest extends Request
{
    public $userID;
    public $taskID;
    public $day;
    public $requesterUserID;
}
