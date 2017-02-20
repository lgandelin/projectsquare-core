<?php

namespace Webaccess\ProjectSquare\Requests\Phases;

use Webaccess\ProjectSquare\Requests\Request;

class GetPhaseRequest extends Request
{
    public $phaseID;
    public $requesterUserID;
}