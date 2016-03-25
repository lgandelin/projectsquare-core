<?php

namespace Webaccess\ProjectSquare;

class Context
{
    public static $objects;

    public function __construct()
    {
        self::$objects = [];
    }

    public static function get($identifier)
    {
        return (isset(self::$objects[$identifier])) ? self::$objects[$identifier] : false;
    }

    public static function set($identifier, $object)
    {
        self::$objects[$identifier] = $object;
    }
}
