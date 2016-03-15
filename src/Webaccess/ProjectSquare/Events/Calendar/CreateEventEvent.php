<?php

namespace Webaccess\ProjectSquare\Events\Calendar;

use Webaccess\ProjectSquare\Entities\Event;

class CreateEventEvent
{
    public $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }
}