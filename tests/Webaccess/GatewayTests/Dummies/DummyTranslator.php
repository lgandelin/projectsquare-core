<?php

namespace Webaccess\GatewayTests\Dummies;

use Webaccess\Gateway\Contracts\Translator;

class DummyTranslator implements Translator
{
    public function translate($string)
    {
        return $string;
    }
}