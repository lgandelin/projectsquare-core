<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\UpdateTaskEvent;
use Webaccess\ProjectSquare\Interactors\Tasks\UpdateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\UpdateTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\UpdateTaskResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateTaskInteractor($this->taskRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateWithoutPermission()
    {
        $user = $this->createSampleUser();
        $task = $this->createSampleTask($user->id);

        $this->interactor->execute(new UpdateTaskRequest([
            'taskID' => $task->id,
            'requesterUserID' => 2
        ]));
    }
    
    public function testUpdate()
    {
        $user = $this->createSampleUser();
        $task = $this->createSampleTask($user->id);

        $response = $this->interactor->execute(new UpdateTaskRequest([
            'taskID' => $task->id,
            'name' => 'New task name',
            'requesterUserID' => $user->id
        ]));
        
        //Check response
        $this->assertInstanceOf(UpdateTaskResponse::class, $response);

        $this->assertEquals('New task name', $response->task->name);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TASK,
            Mockery::type(UpdateTaskEvent::class)
        );
        
    }
}
