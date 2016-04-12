<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\DeleteTaskEvent;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\DeleteTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\DeleteTaskResponse;

class DeleteTaskInteractor
{
    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DeleteTaskRequest $request)
    {
        $task = $this->getTask($request->taskID);
        $this->validateRequest($request, $task);
        $this->deleteTask($task);
        $this->dispatchEvent($task);

        return new DeleteTaskResponse([
            'task' => $task,
        ]);
    }

    private function validateRequest(DeleteTaskRequest $request, Task $task)
    {
        $this->validateRequesterPermissions($request, $task);
    }

    private function validateRequesterPermissions(DeleteTaskRequest $request, Task $task)
    {
        if (!$this->isUserAuthorizedToDeleteTask($request->requesterUserID, $task)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_delete_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteTask($userID, Task $task)
    {
        return $userID == $task->userID;
    }

    private function getTask($taskID)
    {
        if (!$task = $this->repository->getTask($taskID)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_not_found'));
        }

        return $task;
    }

    private function deleteTask(Task $event)
    {
        $this->repository->removeTask($event->id);
    }

    private function dispatchEvent(Task $event)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::DELETE_TASK,
            new DeleteTaskEvent($event)
        );
    }
}
