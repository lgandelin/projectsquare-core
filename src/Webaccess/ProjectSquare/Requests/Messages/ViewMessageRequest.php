<?php

namespace Webaccess\ProjectSquare\Requests\Messages;

use Webaccess\ProjectSquare\Requests\Request;

class ViewMessageRequest extends Request
{
    public $messageID;
    public $requesterUserID;
}