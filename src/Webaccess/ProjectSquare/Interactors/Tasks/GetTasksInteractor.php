<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\GetTasksRequest;

class GetTasksInteractor
{
    public function __construct(TaskRepository $taskRepository)
    {
        $this->repository = $taskRepository;
    }

    public function execute(GetTasksRequest $request)
    {
        return $this->repository->getTasks($request->projectID, $request->statusID, $request->allocatedUserID, $request->entities);
    }

    public function getTasksPaginatedList($limit, GetTasksRequest $request)
    {
        return $this->repository->getTasksPaginatedList($limit, $request->projectID, $request->statusID, $request->allocatedUserID);
    }

    public function getTasksByProjectID($projectID)
    {
        return $this->repository->getTasksByProjectID($projectID);
    }
}