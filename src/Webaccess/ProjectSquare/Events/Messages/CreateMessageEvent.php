<?php

namespace Webaccess\ProjectSquare\Events\Messages;

use Symfony\Component\EventDispatcher\Event;

class CreateMessageEvent extends Event
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }
}
