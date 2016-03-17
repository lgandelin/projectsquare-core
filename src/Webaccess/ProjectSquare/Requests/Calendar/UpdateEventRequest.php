<?php

namespace Webaccess\ProjectSquare\Requests\Calendar;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateEventRequest extends Request
{
    public $eventID;
    public $name;
    public $userID;
    public $startTime;
    public $endTime;
    public $ticketID;
    public $projectID;
    public $requesterUserID;
}