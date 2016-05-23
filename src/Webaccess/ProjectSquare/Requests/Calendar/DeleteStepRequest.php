<?php

namespace Webaccess\ProjectSquare\Requests\Calendar;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteStepRequest extends Request
{
    public $stepID;
    public $requesterUserID;
}
