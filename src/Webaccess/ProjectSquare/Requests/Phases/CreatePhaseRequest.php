<?php

namespace Webaccess\ProjectSquare\Requests\Phases;

use Webaccess\ProjectSquare\Requests\Request;

class CreatePhaseRequest extends Request
{
    public $name;
    public $projectID;
    public $order;
    public $dueDate;
    public $requesterUserID;
}