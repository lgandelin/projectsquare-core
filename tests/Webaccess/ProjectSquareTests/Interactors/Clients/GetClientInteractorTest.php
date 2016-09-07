<?php

use Webaccess\ProjectSquare\Entities\Client;
use Webaccess\ProjectSquare\Interactors\Clients\GetClientInteractor;
use Webaccess\ProjectSquare\Requests\Clients\GetClientRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetClientInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetClientInteractor($this->clientRepository);
    }

    public function testGetNonExistingClient()
    {
        $this->assertFalse($this->interactor->execute(new GetClientRequest([
            'clientID' => 1,
            //'requesterUserID' => $user->id
        ])));
    }

    public function testGetClient()
    {
        $this->createSampleClient();
        $client = $this->interactor->execute(new GetClientRequest([
            'clientID' => 1,
            //'requesterUserID' => $user->id
        ]));

        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('Sample Client', $client->name);
    }
}