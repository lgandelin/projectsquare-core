<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Conversation;

interface ConversationRepository
{
    public function getConversation($conversationID);

    public function persistConversation(Conversation $conversation);
}
