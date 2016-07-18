<?php

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Todos\CreateTodoEvent;
use Webaccess\ProjectSquare\Interactors\Todos\CreateTodoInteractor;
use Webaccess\ProjectSquare\Requests\Todos\CreateTodoRequest;
use Webaccess\ProjectSquare\Responses\Todos\CreateTodoResponse;
use Webaccess\ProjectSquareTests\BaseTestCase;

class CreateTodoInteractorTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->interactor = new CreateTodoInteractor($this->todoRepository);
    }

    public function testCreateTodo()
    {
        $user = $this->createSampleUser();

        $response = $this->interactor->execute(new CreateTodoRequest([
            'name' => 'Sample todo',
            'userID' => $user->id,
            'status' => false,
        ]));

        //Check response
        $this->assertInstanceOf(CreateTodoResponse::class, $response);
        $this->assertEquals('Sample todo', $response->todo->name);

        //Check insertion
        $this->assertCount(1, $this->todoRepository->objects);

        //Check todo
        Context::get('event_dispatcher')->shouldHaveReceived("dispatch")->with(
            Events::CREATE_TODO,
            Mockery::type(CreateTodoEvent::class)
        );
    }
}