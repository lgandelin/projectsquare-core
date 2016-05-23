<?php

namespace Webaccess\ProjectSquare\Requests\Calendar;

use Webaccess\ProjectSquare\Requests\Request;

class CreateStepRequest extends Request
{
    public $name;
    public $projectID;
    public $startTime;
    public $endTime;
    public $color;
    public $requesterUserID;
}
