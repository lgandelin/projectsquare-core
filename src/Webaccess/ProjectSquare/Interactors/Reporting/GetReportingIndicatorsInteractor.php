<?php

namespace Webaccess\ProjectSquare\Interactors\Reporting;

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Interactors\Tasks\GetTasksInteractor;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\GetTasksRequest;

class GetReportingIndicatorsInteractor
{
    public function __construct(TaskRepository $taskRepository)
    {
        $this->repository = $taskRepository;
    }

    public function getTasksCountByStatus($userID, $projectID, $statusID)
    {
        return sizeof((new GetTasksInteractor($this->repository))->execute(new GetTasksRequest([
            'userID' => $userID,
            'projectID' => $projectID,
            'statusID' => $statusID,
        ])));
    }

    public function getProgressPercentage($userID, $projectID, $tasks = [])
    {
        $result = 0;
        if (!is_array($tasks) || sizeof($tasks) == 0) {
            $tasks = (new GetTasksInteractor($this->repository))->execute(new GetTasksRequest([
                'userID' => $userID,
                'projectID' => $projectID,
            ]));
        }

        if (sizeof($tasks) > 0) {
            $result = floor($this->getTasksCountByStatus($userID, $projectID, Task::COMPLETED) * 100 / sizeof($tasks));
        }

        return $result;
    }

    public function getProfitabilityPercentage($scheduledTimeInDays, $spentTime)
    {
        $spentTimeInDays = $spentTime->days + $spentTime->hours / GetTasksTotalTimeInteractor::HOURS_IN_DAY;

        if ($scheduledTimeInDays == 0 || $spentTimeInDays == $scheduledTimeInDays) {
            return 0;
        }

        return -floor(100 * ($spentTimeInDays / $scheduledTimeInDays - 1));
    }
}