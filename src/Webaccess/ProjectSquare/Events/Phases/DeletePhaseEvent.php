<?php

namespace Webaccess\ProjectSquare\Events\Phases;

use Webaccess\ProjectSquare\Entities\Event;

class DeletePhaseEvent extends Event
{
    public $phaseID;

    public function __construct($phaseID)
    {
        $this->phaseID = $phaseID;
    }
}
