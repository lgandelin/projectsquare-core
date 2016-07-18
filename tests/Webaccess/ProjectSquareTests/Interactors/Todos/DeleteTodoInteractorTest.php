<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Todo;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Todos\DeleteTodoEvent;
use Webaccess\ProjectSquare\Interactors\Todos\DeleteTodoInteractor;
use Webaccess\ProjectSquare\Requests\Todos\DeleteTodoRequest;
use Webaccess\ProjectSquare\Responses\Todos\DeleteTodoResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class DeleteTodoInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new DeleteTodoInteractor($this->todoRepository);
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteNonExistingTodo()
    {
        $this->interactor->execute(new DeleteTodoRequest([
            'todoID' => 1,
        ]));
    }

    /**
     * @expectedException Exception
     */
    public function testDeleteWithoutPermission()
    {
        $user1 = $this->createSampleUser();
        $user2 = $this->createSampleUser();
        $todo = $this->createSampleTodo($user1->id);
        $this->interactor->execute(new DeleteTodoRequest([
            'todoID' => $todo->id,
            'requesterUserID' => $user2->id
        ]));
    }

    public function testDeleteTodo()
    {
        $user = $this->createSampleUser();
        $todo = $this->createSampleTodo($user->id);
        $response = $this->interactor->execute(new DeleteTodoRequest([
            'todoID' => $todo->id,
            'requesterUserID' => $user->id
        ]));

        //Check response
        $this->assertInstanceOf(DeleteTodoResponse::class, $response);
        $this->assertInstanceOf(Todo::class, $response->todo);
        $this->assertEquals($todo->id, $response->todo->id);

        //Check deletion
        $this->assertCount(0, $this->todoRepository->objects);

        //Check event
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::DELETE_TODO,
            Mockery::type(DeleteTodoEvent::class)
        );
    }
}