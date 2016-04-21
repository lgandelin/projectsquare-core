<?php

namespace Webaccess\ProjectSquare\Requests\Steps;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateStepRequest extends Request
{
    public $stepID;
    public $name;
    public $projectID;
    public $startTime;
    public $endTime;
    public $color;
    public $requesterUserID;
}
