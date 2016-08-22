<?php

namespace Webaccess\ProjectSquare\Interactors\Todos;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Todo;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Todos\UpdateTodoEvent;
use Webaccess\ProjectSquare\Repositories\TodoRepository;
use Webaccess\ProjectSquare\Requests\Todos\UpdateTodoRequest;
use Webaccess\ProjectSquare\Responses\Todos\UpdateTodoResponse;

class UpdateTodoInteractor
{
    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UpdateTodoRequest $request)
    {
        $todo = $this->getTodo($request->todoID);
        $this->validateRequest($todo, $request);
        $this->updateTodo($todo, $request);
        $this->dispatchEvent($todo);

        return new UpdateTodoResponse([
            'todo' => $todo,
        ]);
    }

    private function getTodo($todoID)
    {
        if (!$todo = $this->repository->getTodo($todoID)) {
            throw new \Exception(Context::get('translator')->translate('todos.todo_not_found'));
        }

        return $todo;
    }

    private function validateRequest(Todo $todo, UpdateTodoRequest $request)
    {
        if (!$this->isUserAuthorizedToUpdateTodo($request->requesterUserID, $todo)) {
            throw new \Exception(Context::get('translator')->translate('todos.update_not_allowed'));
        }
    }

    private function isUserAuthorizedToUpdateTodo($userID, $todo)
    {
        return $userID == $todo->userID;
    }

    private function updateTodo(Todo $todo, UpdateTodoRequest $request)
    {
        if ($request->name) {
            $todo->name = $request->name;
        }
        $todo->status = $request->status;

        $this->repository->persistTodo($todo);
    }

    private function dispatchEvent(Todo $todo)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_TODO,
            new UpdateTodoEvent($todo)
        );
    }
}
