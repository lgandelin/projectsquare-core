<?php

namespace Webaccess\GatewayLaravelTests\Dummies;

use Webaccess\GatewayLaravel\EventManager;

class DummyEventManager implements EventManager
{
    public static $firedEvents;

    public function __construct()
    {
        self::$firedEvents = [];
    }

    public function fire($event)
    {
        self::$firedEvents[]= $event;
    }

    public function getFiredEvents()
    {
        return self::$firedEvents;
    }
}