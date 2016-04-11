<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\UpdateTaskEvent;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\UpdateTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\UpdateTaskResponse;

class UpdateTaskInteractor
{
    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UpdateTaskRequest $request)
    {
        $task = $this->getTask($request->taskID);
        $this->validateRequest($task, $request);
        $this->updateTask($task, $request);
        $this->dispatchEvent($task);

        return new UpdateTaskResponse([
            'task' => $task,
        ]);
    }

    private function getTask($taskID)
    {
        if (!$task = $this->repository->getTask($taskID)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_not_found'));
        }

        return $task;
    }

    private function validateRequest(Task $task, UpdateTaskRequest $request)
    {
        if (!$this->isUserAuthorizedToUpdateTask($request->requesterUserID, $task)) {
            throw new \Exception(Context::get('translator')->translate('tasks.update_not_allowed'));
        }
    }

    private function isUserAuthorizedToUpdateTask($userID, $task)
    {
        return $userID == $task->userID;
    }

    private function updateTask(Task $task, UpdateTaskRequest $request)
    {
        if ($request->name) {
            $task->name = $request->name;
        }
        if ($request->status) {
            $task->status = $request->status;
        }

        $this->repository->persistTask($task);
    }

    private function dispatchEvent(Task $task)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_TASK,
            new UpdateTaskEvent($task)
        );
    }
}