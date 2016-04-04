<?php

namespace Webaccess\ProjectSquare\Requests\Notifications;

use Webaccess\ProjectSquare\Requests\Request;

class CreateNotificationRequest extends Request
{
    public $userID;
    public $type;
    public $entityID;
}
