<?php

namespace Webaccess\ProjectSquare\Interactors\Todos;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Todo;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Todos\DeleteTodoEvent;
use Webaccess\ProjectSquare\Repositories\TodoRepository;
use Webaccess\ProjectSquare\Requests\Todos\DeleteTodoRequest;
use Webaccess\ProjectSquare\Responses\Todos\DeleteTodoResponse;

class DeleteTodoInteractor
{
    public function __construct(TodoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DeleteTodoRequest $request)
    {
        $todo = $this->getTodo($request->todoID);
        $this->validateRequest($request, $todo);
        $this->deleteTodo($todo);
        $this->dispatchEvent($todo);

        return new DeleteTodoResponse([
            'todo' => $todo,
        ]);
    }

    private function validateRequest(DeleteTodoRequest $request, Todo $todo)
    {
        $this->validateRequesterPermissions($request, $todo);
    }

    private function validateRequesterPermissions(DeleteTodoRequest $request, Todo $todo)
    {
        if (!$this->isUserAuthorizedToDeleteTodo($request->requesterUserID, $todo)) {
            throw new \Exception(Context::get('translator')->translate('todos.todo_delete_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteTodo($userID, Todo $todo)
    {
        return $userID == $todo->userID;
    }

    private function getTodo($todoID)
    {
        if (!$todo = $this->repository->getTodo($todoID)) {
            throw new \Exception(Context::get('translator')->translate('todos.todo_not_found'));
        }

        return $todo;
    }

    private function deleteTodo(Todo $event)
    {
        $this->repository->removeTodo($event->id);
    }

    private function dispatchEvent(Todo $event)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::DELETE_TODO,
            new DeleteTodoEvent($event)
        );
    }
}
