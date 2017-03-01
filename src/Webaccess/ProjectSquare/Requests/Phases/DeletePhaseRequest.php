<?php

namespace Webaccess\ProjectSquare\Requests\Phases;

use Webaccess\ProjectSquare\Requests\Request;

class DeletePhaseRequest extends Request
{
    public $phaseID;
    public $requesterUserID;
}