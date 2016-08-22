<?php

namespace Webaccess\ProjectSquare\Requests\Planning;

use Webaccess\ProjectSquare\Requests\Request;

class GetEventsRequest extends Request
{
    public $userID;
    public $projectID;
    public $ticketID;
    public $taskID;
}
