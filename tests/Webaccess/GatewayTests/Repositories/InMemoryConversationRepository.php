<?php

namespace Webaccess\GatewayTests\Repositories;

use Webaccess\Gateway\Entities\Conversation;
use Webaccess\Gateway\Repositories\ConversationRepository;

class InMemoryConversationRepository implements ConversationRepository
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

    public function getConversation($conversationID)
    {
        if (isset($this->objects[$conversationID])) {
            return $this->objects[$conversationID];
        }

        return false;
    }

    public function getConversations()
    {
        // TODO: Implement getConversations() method.
    }

    public function getConversationsPaginatedList($limit)
    {
        // TODO: Implement getConversationsPaginatedList() method.
    }

    public function persistConversation(Conversation $conversation)
    {
        if (!isset($conversation->id)) {
            $conversation->id = self::getNextID();
        }
        $this->objects[$conversation->id]= $conversation;

        return $conversation;
    }

    public function deleteConversation($conversationID)
    {
        // TODO: Implement deleteConversation() method.
    }
}