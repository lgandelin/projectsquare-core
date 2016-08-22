<?php

namespace Webaccess\ProjectSquare\Interactors\Todos;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Todo;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Todos\CreateTodoEvent;
use Webaccess\ProjectSquare\Repositories\TodoRepository;
use Webaccess\ProjectSquare\Requests\Todos\CreateTodoRequest;
use Webaccess\ProjectSquare\Responses\Todos\CreateTodoResponse;

class CreateTodoInteractor
{
    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CreateTodoRequest $request)
    {
        $todo = $this->createTodo($request);
        $this->dispatchTodo($todo);

        return new CreateTodoResponse([
            'todo' => $todo,
        ]);
    }

    private function createTodo(CreateTodoRequest $request)
    {
        $todo = new Todo();
        $todo->name = $request->name;
        $todo->userID = $request->userID;
        $todo->status = $request->status;

        return $this->repository->persistTodo($todo);
    }

    private function dispatchTodo(Todo $todo)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_TODO,
            new CreateTodoEvent($todo)
        );
    }
}
