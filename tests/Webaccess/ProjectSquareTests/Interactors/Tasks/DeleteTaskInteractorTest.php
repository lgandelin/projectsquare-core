<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\DeleteTaskEvent;
use Webaccess\ProjectSquare\Interactors\Tasks\DeleteTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\DeleteTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\DeleteTaskResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteTaskInteractor($this->taskRepository);
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
    public function testDeleteWithoutPermission()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $task = $this->createSampleTask($user1->id);
        $this->interactor->execute(new DeleteTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => $user2->id
        ]));
    }

    public function testDeleteTask()
    {
        $user = $this->createSampleUser();
        $task = $this->createSampleTask($user->id);
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

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::DELETE_TASK,
            Mockery::type(DeleteTaskEvent::class)
        );
    }
}