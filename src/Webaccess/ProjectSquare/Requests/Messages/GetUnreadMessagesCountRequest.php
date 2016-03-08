<?php

namespace Webaccess\ProjectSquare\Requests\Messages;

use Webaccess\ProjectSquare\Requests\Request;

class GetUnreadMessagesCountRequest extends Request
{
    public $userID;
}