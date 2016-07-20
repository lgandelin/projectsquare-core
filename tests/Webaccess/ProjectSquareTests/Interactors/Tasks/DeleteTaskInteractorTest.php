<?php

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Tasks\DeleteTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\DeleteTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\DeleteTaskResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteTaskInteractor($this->taskRepository, $this->projectRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingTask()
    {
        $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => 1,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteTaskWithoutPermission()
    {
        $project = $this->createSampleProject();
        $user = $this->createSampleUser();
        $task = $this->createSampleTask($project->id);
        $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => $user->id
        ]));
    }

    public function testDeleteTask()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->projectRepository->addUserToProject($project, $user, null);
        $task = $this->createSampleTask($project->id);
        $response = $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteTaskResponse::class, $response);
        $this->assertInstanceOf(Task::class, $response->task);
        $this->assertEquals($task->id, $response->task->id);

        //Check deletion
        $this->assertCount(0, $this->taskRepository->objects);
    }
}