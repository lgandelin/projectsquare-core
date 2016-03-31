<?php

namespace Webaccess\ProjectSquare\Events\Messages;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Conversation;
use Webaccess\ProjectSquare\Entities\Message;

class CreateConversationEvent extends Event
{
    public $conversation;
    public $message;

    public function __construct(Conversation $conversation, Message $message)
    {
        $this->conversation = $conversation;
        $this->message = $message;
    }
}
