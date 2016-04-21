<?php

namespace Webaccess\ProjectSquare\Events\Events;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Event as EventEntity;

class CreateEventEvent extends Event
{
    public $event;

    public function __construct(EventEntity $event)
    {
        $this->event = $event;
    }
}
