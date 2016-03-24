<?php

namespace Webaccess\ProjectSquare\Requests\Calendar;

use Webaccess\ProjectSquare\Requests\Request;

class GetEventRequest extends Request
{
    public $eventID;
    public $requesterEventID;
}