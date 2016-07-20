<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;
use Webaccess\ProjectSquare\Responses\Tasks\CreateTaskResponse;

class CreateTaskInteractor
{
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(CreateTaskRequest $request)
    {
        $task = $this->createTicket($request);

        return new CreateTaskResponse([
            'task' => $task,
        ]);
    }

    private function createTicket(CreateTaskRequest $request)
    {
        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->estimatedTime = $request->estimatedTime;
        $task->statusID = $request->statusID;
        $task->allocatedUserID = $request->allocatedUserID;

        if ($request->projectID) {
            $this->validateProject($request->projectID);
            $task->projectID = $request->projectID;
        }

        return $this->repository->persistTask($task);
    }

    private function validateProject($projectID)
    {
        if (!$project = $this->projectRepository->getProject($projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }
}