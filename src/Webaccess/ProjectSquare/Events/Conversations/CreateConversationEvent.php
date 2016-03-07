<?php

namespace Webaccess\ProjectSquare\Events\Conversations;

use Symfony\Component\EventDispatcher\Event;

class CreateConversationEvent extends Event
{
    public $conversation;
    public $message;

    public function __construct($conversation, $message)
    {
        $this->conversation = $conversation;
        $this->message = $message;
    }
}
