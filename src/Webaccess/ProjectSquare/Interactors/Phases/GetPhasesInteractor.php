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
                $completedTasksEstimatedDuration = 0;
                $completedTasksSpentTimeDuration = 0;

                foreach ($phase->tasks as $task) {
                    $phase->estimatedDuration += $task->estimatedTimeDays;
                    $task->spentTimeStatus = 0;

                    if ($task->statusID == Task::COMPLETED) {
                        $completedTasksEstimatedDuration += $task->estimatedTimeDays;
                        $completedTasksSpentTimeDuration += $task->spentTimeDays;

                        $task->differenceSpentEstimated = $task->spentTimeDays - $task->estimatedTimeDays;

                        if ($task->differenceSpentEstimated < 0) {
                            $task->spentTimeStatus = Task::SPENT_TIME_AHEAD;
                        } elseif ($task->differenceSpentEstimated > 0) {
                            $task->spentTimeStatus = Task::SPENT_TIME_EXCEEDED;
                        } else {
                            $task->spentTimeStatus = Task::SPENT_TIME_NORMAL;
                        }
                    }
                }

                $percentage = ($phase->estimatedDuration > 0) ? 100 * ($completedTasksEstimatedDuration / $phase->estimatedDuration) : 0;
                $phase->progress = round($percentage, 0);

                $phase->spentTimeStatus = 0;

                $phase->differenceSpentEstimated = $completedTasksSpentTimeDuration - $completedTasksEstimatedDuration;
            }
        }

        return $phases;
    }
}