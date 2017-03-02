<?php

namespace Webaccess\ProjectSquare\Requests\Projects;

use Webaccess\ProjectSquare\Requests\Request;

class GetProjectProgressRequest extends Request
{
    public $projectID;
    public $phases;
}