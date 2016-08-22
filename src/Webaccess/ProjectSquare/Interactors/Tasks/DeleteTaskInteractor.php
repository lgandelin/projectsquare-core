<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\DeleteTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\DeleteTaskResponse;

class DeleteTaskInteractor
{
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(DeleteTaskRequest $request)
    {
        $task = $this->getTask($request->taskID);
        $this->validateRequest($request, $task);
        $this->deleteTask($task);

        return new DeleteTaskResponse([
            'task' => $task,
        ]);
    }

    private function validateRequest(DeleteTaskRequest $request, Task $task)
    {
        $this->validateRequesterPermissions($request, $task);
    }

    private function getTask($taskID)
    {
        if (!$task = $this->repository->getTask($taskID)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_not_found'));
        }

        return $task;
    }

    private function deleteTask(Task $task)
    {
        $this->repository->deleteTask($task->id);
    }

    private function validateRequesterPermissions(DeleteTaskRequest $request, Task $task)
    {
        if (!$this->isUserAuthorizedToDeleteTask($request->requesterUserID, $task)) {
            throw new \Exception(Context::get('translator')->translate('users.task_deletion_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteTask($userID, Task $task)
    {
        return $this->projectRepository->isUserInProject($task->projectID, $userID);
    }


}