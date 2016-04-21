<?php

namespace Webaccess\ProjectSquare\Events\Steps;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Step;

class UpdateStepEvent extends Event
{
    public $step;

    public function __construct(Step $step)
    {
        $this->step = $step;
    }
}
