<?php

use Webaccess\ProjectSquare\Interactors\Clients\UpdateClientInteractor;
use Webaccess\ProjectSquare\Requests\Clients\UpdateClientRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateClientInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateClientInteractor($this->clientRepository);
    }

    public function testUpdateClient()
    {
        $this->assertCount(0, $this->clientRepository->objects);

        $client = $this->createSampleClient();
        $this->interactor->execute(new UpdateClientRequest([
            'clientID' => $client->id,
            'name' => 'Client modifié',
        ]));

        $this->assertEquals('Client modifié', $this->clientRepository->objects[$client->id]->name);
    }
}