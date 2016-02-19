<?php

namespace Webaccess\GatewayLaravelTests\Dummies;

class DummyTranslator
{
    public function translate($string)
    {
        return $string;
    }
}