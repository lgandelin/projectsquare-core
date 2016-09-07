<?php

use Webaccess\ProjectSquare\Entities\Client;
use Webaccess\ProjectSquare\Interactors\Clients\GetClientsInteractor;
use Webaccess\ProjectSquare\Requests\Clients\GetClientsRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetClientsInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetClientsInteractor($this->clientRepository);
    }

    public function tearDown()
    {
        $this->clientRepository->objects = [];
    }

    public function testGetClients()
    {
        $client1 = new Client();
        $client1->name = "Client 1";

        $client2 = new Client();
        $client2->name = "Client 2";

        $this->clientRepository->objects = [
            $client1,
            $client2
        ];

        $this->assertCount(2, $this->interactor->execute(new GetClientsRequest()));
    }
}