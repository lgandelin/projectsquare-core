<?php

namespace Webaccess\ProjectSquare\Requests\Planning;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteEventRequest extends Request
{
    public $eventID;
    public $requesterUserID;
}
