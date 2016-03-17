<?php

namespace Webaccess\ProjectSquare\Events\Calendar;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Event as EventEntity;

class DeleteEventEvent extends Event
{
    public $event;

    public function __construct(EventEntity $event)
    {
        $this->event = $event;
    }
}