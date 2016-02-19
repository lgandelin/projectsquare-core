<?php

namespace Webaccess\GatewayLaravelTests\Dummies;

use Webaccess\GatewayLaravel\EventManager;

class DummyEventManager implements EventManager
{
    public function fire($event)
    {
    }
}