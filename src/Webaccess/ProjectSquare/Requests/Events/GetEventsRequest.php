<?php

namespace Webaccess\ProjectSquare\Requests\Events;

use Webaccess\ProjectSquare\Requests\Request;

class GetEventsRequest extends Request
{
    public $userID;
    public $projectID;
}
