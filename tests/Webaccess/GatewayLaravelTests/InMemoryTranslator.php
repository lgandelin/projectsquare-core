<?php

namespace Webaccess\GatewayLaravelTests;

class InMemoryTranslator
{
    public function translate($string)
    {
        return $string;
    }
}