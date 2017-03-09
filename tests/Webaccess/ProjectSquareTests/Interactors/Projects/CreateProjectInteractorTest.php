<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Projects\CreateProjectEvent;
use Webaccess\ProjectSquare\Interactors\Projects\CreateProjectInteractor;
use Webaccess\ProjectSquare\Requests\Projects\CreateProjectRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateProjectInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateProjectInteractor($this->projectRepository, $this->userRepository, $this->clientRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateProjectWithNonExistingClient()
    {
        $this->interactor->execute(new CreateProjectRequest([
            'name' => 'Nouveau projet',
            'clientID' => 1,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testCreateProjectWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->interactor->execute(new CreateProjectRequest([
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testCreateProject()
    {
        $this->assertCount(0, $this->projectRepository->objects);
        $client = $this->createSampleClient();

        $response = $this->interactor->execute(new CreateProjectRequest([
            'name' => 'Nouveau projet',
            'clientID' => $client->id,
            'statusID' => 2,
        ]));

        $this->assertCount(1, $this->projectRepository->objects);
        $this->assertInstanceOf(Project::class, $response->project);
        $this->assertEquals('Nouveau projet', $response->project->name);
        $this->assertEquals(2, $response->project->statusID);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_PROJECT,
            Mockery::type(CreateProjectEvent::class)
        );
    }
}