<?php

namespace Webaccess\ProjectSquare\Events\Projects;

use Symfony\Component\EventDispatcher\Event;

class CreateProjectEvent extends Event
{
    public $projectID;

    public function __construct($projectID)
    {
        $this->projectID = $projectID;
    }
}
