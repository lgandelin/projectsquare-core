<?php

namespace Webaccess\ProjectSquare\Requests\Notifications;

use Webaccess\ProjectSquare\Requests\Request;

class GetUnreadNotificationsRequest extends Request
{
    public $userID;
}