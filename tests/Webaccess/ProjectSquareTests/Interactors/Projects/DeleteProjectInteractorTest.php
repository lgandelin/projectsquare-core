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
        $this->interactor = new DeleteProjectInteractor($this->projectRepository, $this->ticketRepository, $this->eventRepository, $this->notificationRepository);
    }

    public function testDeleteProject()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
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
        $project = $this->createSampleProject($project->id);
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
        $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => 2,
            //'requesterUserID' => $user->id
        ]));
    }

    public function testDeleteProjectAlongWithTickets()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project, $user, null);
        $ticket1ID = $this->createSampleTicket('Sample ticket 1', $project->id, 'Lorem ipsum dolor sit amet');
        $ticket2ID = $this->createSampleTicket('Sample ticket 2', $project->id, 'Lorem ipsum dolor sit amet');
        $this->assertCount(2, $this->ticketRepository->objects);

        $response = $this->interactor->execute(new DeleteProjectRequest([
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));

        //Check deletion
        $this->assertCount(0, $this->ticketRepository->objects);
    }
}