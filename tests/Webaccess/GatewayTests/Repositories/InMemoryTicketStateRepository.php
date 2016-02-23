<?php

namespace Webaccess\GatewayTests\Repositories;

class InMemoryTicketStateRepository
{
    public static $objects;

    public function __construct()
    {
        self::$objects = [];
    }

    public static function getNextID()
    {
        return count(self::$objects) + 1;
    }
}