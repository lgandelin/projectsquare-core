<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Webaccess\Gateway\Context as GatewayContext;
use Webaccess\GatewayTests\Dummies\DummyEventManager;
use Webaccess\GatewayTests\Dummies\DummyTranslator;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends TestCase implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        GatewayContext::set('translator', new DummyTranslator());
        GatewayContext::set('event_manager', new DummyEventManager());
    }
}