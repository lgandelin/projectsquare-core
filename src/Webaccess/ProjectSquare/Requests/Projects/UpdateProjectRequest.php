<?php

namespace Webaccess\ProjectSquare\Requests\Projects;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateProjectRequest extends Request
{
    public $projectID;
    public $name;
    public $color;
    public $clientID;
    public $statusID;
    public $requesterUserID;
    public $websiteFrontURL;
    public $websiteBackURL;
}