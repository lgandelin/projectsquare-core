<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\UpdateTaskRequest;

class UpdateTaskInteractor
{
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(UpdateTaskRequest $request)
    {
        $task = $this->getTask($request->taskID);

        if ($request->title) {
            $task->title = $request->title;
        }

        if ($request->projectID) {
            $this->validateProject($request->projectID);
            $task->projectID = $request->projectID;
        }

        $this->repository->persistTask($task);
    }

    private function getTask($taskID)
    {
        if (!$task = $this->repository->getTask($taskID)) {
            throw new \Exception(Context::get('translator')->translate('tasks.task_not_found'));
        }

        return $task;
    }

    private function validateProject($projectID)
    {
        if (!$project = $this->projectRepository->getProject($projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }
}