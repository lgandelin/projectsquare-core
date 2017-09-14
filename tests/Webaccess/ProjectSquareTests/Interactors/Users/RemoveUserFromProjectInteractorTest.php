<?php

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Users\RemoveUserFromProjectInteractor;
use Webaccess\ProjectSquare\Requests\Users\RemoveUserFromProjectRequest;
use Webaccess\ProjectSquare\Responses\Users\RemoveUserFromProjectResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class RemoveUserFromProjectInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new RemoveUserFromProjectInteractor($this->userRepository, $this->projectRepository, $this->taskRepository, $this->eventRepository, $this->notificationRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testRemoveNonExistingUser()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->interactor->execute(new RemoveUserFromProjectRequest([
            'userID' => 3,
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testRemoveUserFromNonExistingProject()
    {
        $user = $this->createSampleUser();
        $this->interactor->execute(new RemoveUserFromProjectRequest([
            'userID' => $user->id,
            'projectID' => 2,
            'requesterUserID' => $user->id
        ]));
    }

    public function testRemoveUserFromProject()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $this->projectRepository->addUserToProject($project->id, $user->id, null);

        $this->createSampleTask($project->id, null, null, Task::TODO, $user->id);
        $this->createSampleTask($project->id, null, null, Task::TODO, $user->id);

        $this->assertCount(2, $this->taskRepository->getTasks($user->id, $project->id, null, $user->id, null, true));

        $response = $this->interactor->execute(new RemoveUserFromProjectRequest([
            'userID' => $user->id,
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));

        $this->assertCount(0, $this->taskRepository->getTasks($user->id, $project->id, null, $user->id, null, true));
        $this->assertInstanceOf(RemoveUserFromProjectResponse::class, $response);
        $this->assertTrue($response->success);
    }
}