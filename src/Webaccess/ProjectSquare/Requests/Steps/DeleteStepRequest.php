<?php

namespace Webaccess\ProjectSquare\Requests\Steps;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteStepRequest extends Request
{
    public $stepID;
    public $requesterUserID;
}
