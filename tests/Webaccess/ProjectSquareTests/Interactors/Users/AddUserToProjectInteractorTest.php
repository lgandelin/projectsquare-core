<?php

use Webaccess\ProjectSquare\Interactors\Users\AddUserToProjectInteractor;
use Webaccess\ProjectSquare\Requests\Users\AddUserToProjectRequest;
use Webaccess\ProjectSquare\Responses\Users\AddUserToProjectResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class AddUserToProjectInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new AddUserToProjectInteractor($this->userRepository, $this->projectRepository, $this->taskRepository, $this->eventRepository, $this->notificationRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testAddNonExistingUser()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->interactor->execute(new AddUserToProjectRequest([
            'userID' => 3,
            'projectID' => $project->id,
            'requesterUserID' => $user->id
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testAddUserToNonExistingProject()
    {
        $user = $this->createSampleUser();
        $this->interactor->execute(new AddUserToProjectRequest([
            'userID' => $user->id,
            'projectID' => 2,
            'requesterUserID' => $user->id
        ]));
    }

    public function testAddUserToProject()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $response = $this->interactor->execute(new AddUserToProjectRequest([
            'userID' => $user->id,
            'projectID' => $project->id,
            'roleID' => 1,
            'requesterUserID' => $user->id
        ]));

        $this->assertInstanceOf(AddUserToProjectResponse::class, $response);
        $this->assertTrue($response->success);
    }

    /**
     * @expectedException Exception
     */
    public function testAddUserAlreadyInProject()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $response = $this->interactor->execute(new AddUserToProjectRequest([
            'userID' => $user->id,
            'projectID' => $project->id,
            'roleID' => 1,
            'requesterUserID' => $user->id
        ]));

        $this->assertInstanceOf(AddUserToProjectResponse::class, $response);
        $this->assertTrue($response->success);

        $this->interactor->execute(new AddUserToProjectRequest([
            'userID' => $user->id,
            'projectID' => $project->id,
            'roleID' => 1,
            'requesterUserID' => $user->id
        ]));
    }
}