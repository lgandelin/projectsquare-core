<?php

namespace Webaccess\ProjectSquare\Interactors\Tasks;

use Webaccess\ProjectSquare\Repositories\TaskRepository;

class GetTaskInteractor
{
    public function __construct(TaskRepository $taskRepository)
    {
        $this->repository = $taskRepository;
    }

    public function execute($request)
    {
        return $this->repository->getTask($request->taskID);
    }
}