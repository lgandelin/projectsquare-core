<?php

namespace Webaccess\ProjectSquare\Requests\Messages;

use Webaccess\ProjectSquare\Requests\Request;

class CreateConversationRequest extends Request
{
    public $title;
    public $message;
    public $projectID;
    public $requesterUserID;
}
