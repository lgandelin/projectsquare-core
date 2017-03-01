<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Projects\UpdateProjectEvent;
use Webaccess\ProjectSquare\Interactors\Projects\UpdateProjectInteractor;
use Webaccess\ProjectSquare\Requests\Projects\UpdateProjectRequest;
use Webaccess\ProjectSquare\Responses\Projects\UpdateProjectResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateProjectInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateProjectInteractor($this->projectRepository, $this->userRepository, $this->clientRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateNonExistingProject()
    {
        $user = $this->createSampleUser();
        $this->interactor->execute(new UpdateProjectRequest([
            'projectID' => 1,
            'name' => 'New project',
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateProjectWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->interactor->execute(new UpdateProjectRequest([
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateProjectWithNonExistingClient()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser(true);
        $this->interactor->execute(new UpdateProjectRequest([
            'projectID' => $project->id,
            'clientID' => 5,
            'requesterUserID' => $user->id
        ]));
    }

    public function testUpdateProject()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser(true);
        $response = $this->interactor->execute(new UpdateProjectRequest([
            'projectID' => $project->id,
            'name' => 'New project',
            'websiteFrontURL' => 'front url',
            'websiteBackURL' => 'back url',
            'tasksScheduledTime' => 12.0,
            'ticketsScheduledTime' => 3.0,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(UpdateProjectResponse::class, $response);
        $this->assertEquals($project->id, $response->project->id);
        $this->assertEquals('New project', $response->project->name);
        $this->assertEquals('front url', $response->project->websiteFrontURL);
        $this->assertEquals('back url', $response->project->websiteBackURL);
        $this->assertEquals(12.0, $response->project->tasksScheduledTime);
        $this->assertEquals(3.0, $response->project->ticketsScheduledTime);

        //Check update
        $project = $this->projectRepository->getProject($project->id);
        $this->assertEquals('New project', $project->name);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_PROJECT,
            Mockery::type(UpdateProjectEvent::class)
        );
    }
}