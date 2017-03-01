<?php

namespace Webaccess\ProjectSquare\Requests\Projects;

use Webaccess\ProjectSquare\Requests\Request;

class CreateProjectRequest extends Request
{
    public $name;
    public $color;
    public $clientID;
    public $requesterUserID;
}
