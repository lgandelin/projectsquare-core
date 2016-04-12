<?php

namespace Webaccess\ProjectSquare\Requests\Notifications;

use Webaccess\ProjectSquare\Requests\Request;

class ReadNotificationRequest extends Request
{
    public $notificationID;
    public $userID;
}
