<?php

namespace Webaccess\ProjectSquare\Interactors\Projects;

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Requests\Projects\GetProjectProgressRequest;

class GetProjectProgressInteractor
{
    protected $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(GetProjectProgressRequest $request)
    {
        $projectEstimatedDuration = 0;
        $completedTasksTotalDuration = 0;

        if (is_array($request->phases) && sizeof($request->phases) > 0) {
            foreach ($request->phases as $phase) {
                if (is_array($phase->tasks) && sizeof($phase->tasks) > 0) {
                    foreach ($phase->tasks as $task) {
                        $projectEstimatedDuration += $task->estimatedTimeDays;

                        if ($task->statusID == Task::COMPLETED) {
                            $completedTasksTotalDuration += $task->estimatedTimeDays;
                        }
                    }
                }
            }
        }

        $percentage = ($projectEstimatedDuration > 0) ? 100 * ($completedTasksTotalDuration / $projectEstimatedDuration) : 0;

        return round($percentage, 0);
    }
}