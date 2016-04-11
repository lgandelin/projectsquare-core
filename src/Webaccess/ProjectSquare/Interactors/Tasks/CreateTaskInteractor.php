<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\CreateTaskEvent;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\CreateTaskResponse;

class CreateTaskInteractor
{
    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CreateTaskRequest $request)
    {
        $task = $this->createTask($request);
        $this->dispatchTask($task);

        return new CreateTaskResponse([
            'task' => $task,
        ]);
    }

    private function createTask(CreateTaskRequest $request)
    {
        $task = new Task();
        $task->name = $request->name;
        $task->userID = $request->userID;
        $task->status = $request->status;

        return $this->repository->persistTask($task);
    }

    private function dispatchTask($task)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_TASK,
            new CreateTaskEvent($task)
        );
    }
}