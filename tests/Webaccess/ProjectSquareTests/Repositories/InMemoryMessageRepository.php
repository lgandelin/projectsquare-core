<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Message;
use Webaccess\ProjectSquare\Repositories\MessageRepository;

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
        if (isset($this->objects[$messageID])) {
            return $this->objects[$messageID];
        }

        return false;
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

    public function getMessagesByConversation($conversationID)
    {
        $result = [];

        foreach ($this->objects as $message) {
            if ($message->conversationID == $conversationID) {
                $result[]= $message;
            }
        }

        return $result;
    }
}