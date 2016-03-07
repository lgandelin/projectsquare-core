<?php

namespace Webaccess\ProjectSquare\Repositories;

interface MessageUserRepository
{
    public function readMessage($userID, $messageID);

    public function getUnreadMessages($userID);
}