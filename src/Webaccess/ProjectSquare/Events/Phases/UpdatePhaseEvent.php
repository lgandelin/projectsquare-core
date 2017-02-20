<?php

namespace Webaccess\Projectsquare\Events\Phases;

use Webaccess\ProjectSquare\Entities\Event;

class UpdatePhaseEvent extends Event
{
    public $phaseID;

    public function __construct($phaseID)
    {
        $this->phaseID = $phaseID;
    }
}
