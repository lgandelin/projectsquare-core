<?php

use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Interactors\Projects\DeleteProjectInteractor;
use Webaccess\ProjectSquare\Requests\Projects\DeleteProjectRequest;
use Webaccess\ProjectSquare\Responses\Projects\DeleteProjectResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteProjectInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteProjectInteractor($this->projectRepository, $this->userRepository, $this->ticketRepository, $this->taskRepository, $this->eventRepository, $this->notificationRepository);
    }

    public function testDeleteProject()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser(true);
        $response = $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteProjectResponse::class, $response);
        $this->assertInstanceOf(Project::class, $response->project);
        $this->assertEquals($project->id, $response->project->id);

        //Check deletion
        $this->assertCount(0, $this->projectRepository->objects);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteProjectWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingProject()
    {
        $user = $this->createSampleUser();
        $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => 2,
            'requesterUserID' => $user->id
        ]));
    }

    public function testDeleteProjectAlongWithTickets()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser(true);
        $this->createSampleTicket('Sample ticket 1', $project->id, 'Lorem ipsum dolor sit amet');
        $this->createSampleTicket('Sample ticket 2', $project->id, 'Lorem ipsum dolor sit amet');
        $this->assertCount(2, $this->ticketRepository->objects);

        $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));

        //Check deletion
        $this->assertCount(0, $this->ticketRepository->objects);
    }

    public function testDeleteProjectAlongWithTasks()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser(true);
        $this->createSampleTask($project->id);
        $this->createSampleTask($project->id);
        $this->assertCount(2, $this->taskRepository->objects);

        $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));

        //Check deletion
        $this->assertCount(0, $this->taskRepository->objects);
    }
}