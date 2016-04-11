<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\CreateTaskEvent;
use Webaccess\ProjectSquare\Interactors\Tasks\CreateTaskInteractor;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\CreateTaskResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateTaskInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateTaskInteractor($this->taskRepository);
    }

    public function testCreateTask()
    {
        $user = $this->createSampleUser();

        $response = $this->interactor->execute(new CreateTaskRequest([
            'name' => 'Sample task',
            'userID' => $user->id,
        ]));

        //Check response
        $this->assertInstanceOf(CreateTaskResponse::class, $response);
        $this->assertEquals('Sample task', $response->task->name);

        //Check insertion
        $this->assertCount(1, $this->taskRepository->objects);

        //Check task
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TASK,
            Mockery::type(CreateTaskEvent::class)
        );
    }
}