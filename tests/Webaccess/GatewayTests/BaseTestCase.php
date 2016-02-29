<?php

namespace Webaccess\GatewayTests;

use Mockery;
use Webaccess\Gateway\Context;
use Webaccess\GatewayTests\Dummies\DummyTranslator;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        Context::set('translator', new DummyTranslator());
        Context::set('event_dispatcher', Mockery::spy("EventDispatcherInterface"));
    }
}