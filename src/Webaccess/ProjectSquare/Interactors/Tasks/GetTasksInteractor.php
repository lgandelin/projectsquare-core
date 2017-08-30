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
        return $this->repository->getTasks($request->userID, $request->projectID, $request->statusID, $request->allocatedUserID, $request->phaseID, $request->entities);
    }

    public function getTasksPaginatedList($userID, $limit, $sortColumn = null, $sortOrder = null, GetTasksRequest $request = null)
    {
        return $this->repository->getTasksPaginatedList($userID, $limit, $request->projectID, $request->statusID, $request->allocatedUserID, $request->phaseID, $sortColumn, $sortOrder);
    }

    public function getTasksByProjectID($projectID)
    {
        return $this->repository->getTasksByProjectID($projectID);
    }

    public function getTasksByPhaseID($phaseID)
    {
        return $this->repository->getTasksByPhaseID($phaseID);
    }
}