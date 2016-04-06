<?php

namespace Webaccess\ProjectSquare\Requests\Planning;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteStepRequest extends Request
{
    public $stepID;
    public $requesterUserID;
}
