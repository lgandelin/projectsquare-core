<?php

namespace Webaccess\ProjectSquareTests\Dummies;

use Webaccess\ProjectSquare\Contracts\Translator;

class DummyTranslator implements Translator
{
    public function translate($string)
    {
        return $string;
    }
}