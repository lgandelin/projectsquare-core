<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Todos\UpdateTodoEvent;
use Webaccess\ProjectSquare\Interactors\Todos\UpdateTodoInteractor;
use Webaccess\ProjectSquare\Requests\Todos\UpdateTodoRequest;
use Webaccess\ProjectSquare\Responses\Todos\UpdateTodoResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class UpdateTodoInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new UpdateTodoInteractor($this->todoRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testUpdateWithoutPermission()
    {
        $user = $this->createSampleUser();
        $todo = $this->createSampleTodo($user->id);

        $this->interactor->execute(new UpdateTodoRequest([
            'todoID' => $todo->id,
            'requesterUserID' => 2
        ]));
    }
    
    public function testUpdate()
    {
        $user = $this->createSampleUser();
        $todo = $this->createSampleTodo($user->id);

        $response = $this->interactor->execute(new UpdateTodoRequest([
            'todoID' => $todo->id,
            'name' => 'New todo name',
            'requesterUserID' => $user->id
        ]));
        
        //Check response
        $this->assertInstanceOf(UpdateTodoResponse::class, $response);

        $this->assertEquals('New todo name', $response->todo->name);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::UPDATE_TODO,
            Mockery::type(UpdateTodoEvent::class)
        );
        
    }
}
