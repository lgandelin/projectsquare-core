<?php

namespace Webaccess\ProjectSquare\Requests\Projects;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteProjectRequest extends Request
{
    public $projectID;
    public $requesterUserID;
}