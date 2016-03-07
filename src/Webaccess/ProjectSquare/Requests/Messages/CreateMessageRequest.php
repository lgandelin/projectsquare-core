<?php

namespace Webaccess\ProjectSquare\Requests\Messages;

use Webaccess\ProjectSquare\Requests\Request;

class CreateMessageRequest extends Request
{
    public $content;
    public $conversationID;
    public $requesterUserID;
}