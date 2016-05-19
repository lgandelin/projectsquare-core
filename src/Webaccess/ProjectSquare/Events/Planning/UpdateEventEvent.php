<?php

namespace Webaccess\ProjectSquare\Events\Planning;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Event as EventEntity;

class UpdateEventEvent extends Event
{
    public $event;

    public function __construct(EventEntity $event)
    {
        $this->event = $event;
    }
}
