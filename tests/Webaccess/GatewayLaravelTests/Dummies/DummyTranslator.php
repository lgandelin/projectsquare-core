<?php

namespace Webaccess\GatewayLaravelTests\Dummies;

use Webaccess\GatewayLaravel\Contracts\Translator;

class DummyTranslator implements Translator
{
    public function translate($string)
    {
        return $string;
    }
}