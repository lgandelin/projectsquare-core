<?php

namespace Webaccess\ProjectSquare\Requests\Phases;

use Webaccess\ProjectSquare\Requests\Request;

class CreatePhasesAndTasksFromTextRequest extends Request
{
    public $text;
    public $projectID;
    public $requesterUserID;
}