<?php

namespace Webaccess\ProjectSquare\Requests\Steps;

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
