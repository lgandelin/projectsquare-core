<?php

namespace Webaccess\ProjectSquare\Requests\Planning;

use Webaccess\ProjectSquare\Requests\Request;

class CreateEventRequest extends Request
{
    public $name;
    public $userID;
    public $startTime;
    public $endTime;
    public $ticketID;
    public $taskID;
    public $projectID;
    public $requesterUserID;
}
