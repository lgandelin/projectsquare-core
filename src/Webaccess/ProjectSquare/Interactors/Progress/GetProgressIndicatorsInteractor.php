<?php

namespace Webaccess\ProjectSquare\Interactors\Progress;

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Tasks\GetTasksInteractor;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\GetTasksRequest;

class GetProgressIndicatorsInteractor
{
    public function __construct(TaskRepository $taskRepository)
    {
        $this->repository = $taskRepository;
    }

    public function getTasksCountByStatus($projectID, $statusID)
    {
        return sizeof((new GetTasksInteractor($this->repository))->execute(new GetTasksRequest([
            'projectID' => $projectID,
            'statusID' => $statusID,
        ])));
    }

    public function getProgressPercentage($projectID, $tasks = [])
    {
        $result = 0;
        if (!is_array($tasks) || sizeof($tasks) == 0) {
            $tasks = (new GetTasksInteractor($this->repository))->execute(new GetTasksRequest([
                'projectID' => $projectID,
            ]));
        }

        if (sizeof($tasks) > 0) {
            $result = floor($this->getTasksCountByStatus($projectID, Task::COMPLETED) * 100 / sizeof($tasks));
        }

        return $result;
    }

    public function getProfitabilityPercentage($scheduledTimeInDays, $spentTime)
    {
        $spentTimeInDays = $spentTime->days + $spentTime->hours / GetTasksTotalTimeInteractor::HOURS_IN_DAY;

        return ($scheduledTimeInDays > 0) ? -floor(100 * ($spentTimeInDays / $scheduledTimeInDays - 1)) : 0;
    }
}