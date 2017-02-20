<?php

namespace Webaccess\ProjectSquare\Events\Phases;

use Webaccess\ProjectSquare\Entities\Event;

class CreatePhaseEvent extends Event
{
    public $phaseID;

    public function __construct($phaseID)
    {
        $this->phaseID = $phaseID;
    }
}
