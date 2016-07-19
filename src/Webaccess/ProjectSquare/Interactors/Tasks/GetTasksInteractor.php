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
        return $this->repository->getTasks($request->projectID);
    }
}