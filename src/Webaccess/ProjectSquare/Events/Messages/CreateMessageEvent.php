<?php

namespace Webaccess\ProjectSquare\Events\Messages;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Message;

class CreateMessageEvent extends Event
{
    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }
}
