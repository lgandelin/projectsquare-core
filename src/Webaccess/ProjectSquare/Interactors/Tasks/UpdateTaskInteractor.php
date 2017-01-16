<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tasks\UpdateTaskEvent;
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
        if ($request->title !== null) $task->title = $request->title;
        if ($request->description !== null) $task->description = $request->description;
        if ($request->estimatedTimeDays !== null) $task->estimatedTimeDays = $request->estimatedTimeDays;
        if ($request->estimatedTimeHours !== null) $task->estimatedTimeHours = $request->estimatedTimeHours;
        if ($request->spentTimeDays !== null) $task->spentTimeDays = $request->spentTimeDays;
        if ($request->spentTimeHours !== null) $task->spentTimeHours = $request->spentTimeHours;
        if ($request->statusID !== null) $task->statusID = $request->statusID;
        if ($request->allocatedUserID !== null) $task->allocatedUserID = $request->allocatedUserID;

        if ($request->projectID) {
            $this->validateProject($request->projectID);
            $task->projectID = $request->projectID;
        }

        $this->repository->persistTask($task);

        $this->dispatchEvent($task->id);
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

    private function dispatchEvent($taskID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_TASK,
            new UpdateTaskEvent($taskID)
        );
    }
}