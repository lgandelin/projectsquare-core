<?php

use Webaccess\ProjectSquare\Interactors\Clients\CreateClientInteractor;
use Webaccess\ProjectSquare\Requests\Clients\CreateClientRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateClientInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateClientInteractor($this->clientRepository);
    }

    public function testCreateClient()
    {
        $this->assertCount(0, $this->clientRepository->objects);

        $this->interactor->execute(new CreateClientRequest([
            'name' => 'Nouveau client',
        ]));

        $this->assertCount(1, $this->clientRepository->objects);
    }
}