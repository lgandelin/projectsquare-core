<?php

namespace Webaccess\GatewayTests\Repositories;

use Webaccess\Gateway\Entities\Message;
use Webaccess\Gateway\Repositories\MessageRepository;

class InMemoryMessageRepository implements MessageRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    public function getNextID()
    {
        return count($this->objects) + 1;
    }

    public function getMessage($messageID)
    {
        // TODO: Implement getMessage() method.
    }

    public function getMessages()
    {
        // TODO: Implement getMessages() method.
    }

    public function getMessagesPaginatedList($limit)
    {
        // TODO: Implement getMessagesPaginatedList() method.
    }

    public function persistMessage(Message $message)
    {
        if (!isset($message->id)) {
            $message->id = self::getNextID();
        }
        $this->objects[$message->id]= $message;

        return $message;
    }

    public function deleteMessage($messageID)
    {
        // TODO: Implement deleteMessage() method.
    }
}