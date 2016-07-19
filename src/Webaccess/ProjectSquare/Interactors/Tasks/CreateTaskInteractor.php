<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\CreateTaskRequest;

class CreateTaskInteractor
{
    public function __construct(TaskRepository $taskRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(CreateTaskRequest $request)
    {
        $this->validateRequest($request);

        return $this->createTicket($request);
    }

    private function createTicket(CreateTaskRequest $request)
    {
        $task = new Task();
        $task->title = $request->title;
        $task->status = $request->status;
        $task->projectID = $request->projectID;
        $task->startDate = $request->startDate;
        $task->endDate = $request->endDate;

        return $this->repository->persistTask($task);
    }

    private function validateRequest(CreateTaskRequest $request)
    {
        $this->validateProject($request);
    }

    private function validateProject(CreateTaskRequest $request)
    {
        if (!$project = $this->projectRepository->getProject($request->projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }
}