<?php

namespace Webaccess\ProjectSquare\Events\Alerts;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquareLaravel\Models\Request;

class AlertWebsiteStatusCodeEvent extends Event
{
    public $request;
    public $email;
    public $statusCode;

    public function __construct(Request $request, $email, $statusCode)
    {
        $this->request = $request;
        $this->email = $email;
        $this->statusCode = $statusCode;
    }
}