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

    public static function getConversation($conversationID)
    {
        // TODO: Implement getConversation() method.
    }

    public static function getConversations()
    {
        // TODO: Implement getConversations() method.
    }

    public static function getConversationsPaginatedList($limit)
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

    public static function deleteConversation($conversationID)
    {
        // TODO: Implement deleteConversation() method.
    }
}