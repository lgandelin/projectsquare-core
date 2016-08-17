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
        $task->title = $request->title;
        $task->description = $request->description;
        $task->estimatedTimeDays = $request->estimatedTimeDays;
        $task->estimatedTimeHours = $request->estimatedTimeHours;
        $task->spentTimeDays = $request->spentTimeDays;
        $task->spentTimeHours = $request->spentTimeHours;
        $task->statusID = $request->statusID;
        $task->allocatedUserID = $request->allocatedUserID;

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