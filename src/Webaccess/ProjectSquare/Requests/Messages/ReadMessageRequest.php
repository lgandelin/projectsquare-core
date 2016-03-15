<?php

namespace Webaccess\ProjectSquare\Requests\Messages;

use Webaccess\ProjectSquare\Requests\Request;

class ReadMessageRequest extends Request
{
    public $messageID;
    public $requesterUserID;
}
