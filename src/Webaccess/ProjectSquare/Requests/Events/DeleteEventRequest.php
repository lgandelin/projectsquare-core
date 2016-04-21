<?php

namespace Webaccess\ProjectSquare\Requests\Events;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteEventRequest extends Request
{
    public $eventID;
    public $requesterUserID;
}
