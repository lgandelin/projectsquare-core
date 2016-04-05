<?php

namespace Webaccess\ProjectSquare\Requests\Calendar;

use Webaccess\ProjectSquare\Requests\Request;

class GetEventsRequest extends Request
{
    public $userID;
    public $projectID;
}
