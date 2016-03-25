<?php

namespace Webaccess\ProjectSquare\Requests\Calendar;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteEventRequest extends Request
{
    public $eventID;
    public $requesterUserID;
}