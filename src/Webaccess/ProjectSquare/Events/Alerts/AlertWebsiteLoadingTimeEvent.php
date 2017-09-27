<?php

namespace Webaccess\ProjectSquare\Events\Alerts;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquareLaravel\Models\Request;

class AlertWebsiteLoadingTimeEvent extends Event
{
    public $request;
    public $email;
    public $loadingTime;

    public function __construct(Request $request, $email, $loadingTime)
    {
        $this->request = $request;
        $this->email = $email;
        $this->loadingTime = $loadingTime;
    }
}
