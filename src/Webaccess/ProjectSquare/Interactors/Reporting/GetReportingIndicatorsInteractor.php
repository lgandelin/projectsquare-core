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

    public function getProgressPercentage($userID, $projectID, $tasksScheduledTime)
    {
        $totalEstimatedTime = 0;
        $completedTasks = (new GetTasksInteractor($this->repository))->execute(new GetTasksRequest([
            'userID' => $userID,
            'projectID' => $projectID,
            'statusID' => Task::COMPLETED,
            'entities' => true
        ]));

        foreach ($completedTasks as $task) {
            $totalEstimatedTime += $task->estimatedTimeDays + $task->estimatedTimeHours * GetTasksTotalTimeInteractor::HOURS_IN_DAY;
        }

        return $tasksScheduledTime > 0 ? floor(($totalEstimatedTime / $tasksScheduledTime) * 100) : 0;
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