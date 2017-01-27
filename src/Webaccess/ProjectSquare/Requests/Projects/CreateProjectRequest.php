<?php

namespace Webaccess\ProjectSquare\Requests\Projects;

use Webaccess\ProjectSquare\Requests\Request;

class CreateProjectRequest extends Request
{
    public $clientID;
    public $color;
    public $tasksScheduledTime;
    public $ticketsScheduledTime;
    public $websiteFrontURL;
    public $websiteBackURL;
    public $slackChannel;
}
