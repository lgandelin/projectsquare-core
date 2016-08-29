<?php

use Webaccess\ProjectSquare\Entities\Client;
use Webaccess\ProjectSquare\Interactors\Clients\DeleteClientInteractor;
use Webaccess\ProjectSquare\Requests\Clients\DeleteClientRequest;
use Webaccess\ProjectSquare\Responses\Clients\DeleteClientResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteClientInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteClientInteractor($this->clientRepository, $this->projectRepository);
    }

    public function testDeleteClient()
    {
        $client = $this->createSampleClient();
        $response = $this->interactor->execute(new DeleteClientRequest([
            'clientID' => $client->id,
            //'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteClientResponse::class, $response);
        $this->assertInstanceOf(Client::class, $response->client);
        $this->assertEquals($client->id, $response->client->id);

        //Check deletion
        $this->assertCount(0, $this->clientRepository->objects);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingClient()
    {
        $this->interactor->execute(new DeleteClientRequest([
            'clientID' => 2,
            //'requesterUserID' => $user->id
        ]));
    }

    public function testDeleteClientAlongWithProjects()
    {
        $client = $this->createSampleClient();
        $this->createSampleProject($client->id);
        $this->createSampleProject($client->id);
        $this->assertcount(2, $this->projectRepository->objects);

        $response = $this->interactor->execute(new DeleteClientRequest([
            'clientID' => $client->id,
            //'requesterUserID' => $user->id
        ]));

        //Check that projects have been deleted
        $this->assertCount(0, $this->projectRepository->objects);
    }
}