<?php

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Tasks\GetTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\GetTaskRequest;
use Webaccess\ProjectSquareTests\BaseTestCase;

class GetTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new GetTaskInteractor($this->taskRepository);
    }

    public function testGetNonExistingTask()
    {
        $user = $this->createSampleUser();
        $this->assertFalse($this->interactor->execute(new GetTaskRequest([
            'taskID' => 1,
            'requesterUserID' => $user->id
        ])));
    }

    public function testGetTask()
    {
        $user = $this->createSampleUser();
        $project = $this->createSampleProject();
        $this->createSampleTask($project->id);
        $task = $this->interactor->execute(new GetTaskRequest([
            'taskID' => 1,
            'requesterUserID' => $user->id
        ]));

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Sample task', $task->title);
    }
}