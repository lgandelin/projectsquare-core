<?php

namespace Webaccess\ProjectSquare\Events\Phases;

use Symfony\Component\EventDispatcher\Event;

class UpdatePhaseEvent extends Event
{
    public $phaseID;

    public function __construct($phaseID)
    {
        $this->phaseID = $phaseID;
    }
}
