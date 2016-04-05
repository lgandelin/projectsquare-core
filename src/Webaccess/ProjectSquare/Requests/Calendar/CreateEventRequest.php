<?php

namespace Webaccess\ProjectSquare\Requests\Calendar;

use Webaccess\ProjectSquare\Requests\Request;

class CreateEventRequest extends Request
{
    public $name;
    public $userID;
    public $startTime;
    public $endTime;
    public $ticketID;
    public $projectID;
    public $requesterUserID;
}
