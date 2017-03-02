<?php

namespace Webaccess\ProjectSquare\Interactors\Phases;

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Tasks\GetTasksInteractor;
use Webaccess\ProjectSquare\Repositories\PhaseRepository;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Phases\GetPhasesRequest;

class GetPhasesInteractor
{
    public function __construct(PhaseRepository $phaseRepository, TaskRepository $taskRepository)
    {
        $this->repository = $phaseRepository;
        $this->taskRepository = $taskRepository;
    }

    public function execute(GetPhasesRequest $request)
    {
        $phases = $this->repository->getPhases($request->projectID);

        if (is_array($phases) && sizeof($phases) > 0) {
            foreach ($phases as $phase) {
                $phase->tasks = (new GetTasksInteractor($this->taskRepository))->getTasksByPhaseID($phase->id);
                $phase->estimatedDuration = 0;
                $phase->completedTasksTotalDuration = 0;

                foreach ($phase->tasks as $task) {
                    $phase->estimatedDuration += $task->estimatedTimeDays;

                    if ($task->statusID == Task::COMPLETED) {
                        $phase->completedTasksTotalDuration += $task->estimatedTimeDays;
                    }
                }

                $percentage = ($phase->estimatedDuration > 0) ? 100 * ($phase->completedTasksTotalDuration / $phase->estimatedDuration) : 0;
                $phase->progress = round($percentage, 0);
            }
        }

        return $phases;
    }
}