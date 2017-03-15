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
        $this->interactor = new DeleteClientInteractor($this->clientRepository, $this->projectRepository, $this->userRepository, $this->ticketRepository, $this->taskRepository, $this->eventRepository, $this->notificationRepository);
    }

    public function testDeleteClient()
    {
        $client = $this->createSampleClient();
        $user = $this->createSampleUser(true);
        $response = $this->interactor->execute(new DeleteClientRequest([
            'clientID' => $client->id,
            'requesterUserID' => $user->id
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
        $user = $this->createSampleUser(true);
        $this->interactor->execute(new DeleteClientRequest([
            'clientID' => 2,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteProjectWithoutPermission()
    {
        $client = $this->createSampleClient();
        $user = $this->createSampleUser();
        $this->interactor->execute(new DeleteClientRequest([
            'clientID' => $client->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testDeleteClientAlongWithProjects()
    {
        $client = $this->createSampleClient();
        $user = $this->createSampleUser(true);
        $project1 = $this->createSampleProject($client->id);
        $project2 = $this->createSampleProject($client->id);
        $this->projectRepository->addUserToProject($project1->id, $user->id, null);
        $this->projectRepository->addUserToProject($project2->id, $user->id, null);

        $this->assertcount(2, $this->projectRepository->objects);

        $this->interactor->execute(new DeleteClientRequest([
            'clientID' => $client->id,
            'requesterUserID' => $user->id
        ]));

        //Check that projects have been deleted
        $this->assertCount(0, $this->projectRepository->objects);
    }
}