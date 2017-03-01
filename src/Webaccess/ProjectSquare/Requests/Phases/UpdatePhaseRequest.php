<?php

namespace Webaccess\ProjectSquare\Requests\Phases;

use Webaccess\ProjectSquare\Requests\Request;

class UpdatePhaseRequest extends Request
{
    public $phaseID;
    public $name;
    public $order;
    public $dueDate;
    public $requesterUserID;
}