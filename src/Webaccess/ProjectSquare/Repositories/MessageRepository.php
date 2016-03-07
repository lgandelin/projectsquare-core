<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Message;

interface MessageRepository
{
    public function getMessage($messageID);

    public function persistMessage(Message $message);

    public function getMessagesByConversation($conversationID);
}