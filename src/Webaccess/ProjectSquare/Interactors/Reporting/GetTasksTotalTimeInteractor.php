<?php

namespace Webaccess\ProjectSquare\Interactors\Reporting;

use Webaccess\ProjectSquare\Interactors\Tasks\GetTasksInteractor;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\GetTasksRequest;
use Webaccess\ProjectSquare\Responses\Reporting\GetTasksTotalTimeResponse;

class GetTasksTotalTimeInteractor
{
    const HOURS_IN_DAY = 7;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->repository = $taskRepository;
        $this->getTasksInteractor = new GetTasksInteractor($taskRepository);
    }

    public function getTasksTotalEstimatedTime($userID, $projectID)
    {
        $tasks = $this->getTasksInteractor->execute(new GetTasksRequest([
            'userID' => $userID,
            'projectID' => $projectID,
            'entities' => true
        ]));

        $totalEstimatedTimeDays = 0;
        $totalEstimatedTimeHours = 0;

        if (is_array($tasks) && sizeof($tasks) > 0) {
            foreach ($tasks as $task) {
                $totalEstimatedTimeDays += $task->estimatedTimeDays;
                $totalEstimatedTimeHours += $task->estimatedTimeHours;
            }
        }

        if ($totalEstimatedTimeHours >= self::HOURS_IN_DAY) {
            $totalEstimatedTimeDays += floor($totalEstimatedTimeHours / self::HOURS_IN_DAY);
            $totalEstimatedTimeHours = $totalEstimatedTimeHours % self::HOURS_IN_DAY;
        }

        return new GetTasksTotalTimeResponse(['days' => $totalEstimatedTimeDays, 'hours' => $totalEstimatedTimeHours]);
    }

    public function getTasksTotalSpentTime($userID, $projectID)
    {
        $tasks = $this->getTasksInteractor->execute(new GetTasksRequest([
            'userID' => $userID,
            'projectID' => $projectID,
            'entities' => true
        ]));

        $totalSpentTimeDays = 0;
        $totalSpentTimeHours = 0;

        if (is_array($tasks) && sizeof($tasks) > 0) {
            foreach ($tasks as $task) {
                $totalSpentTimeDays += $task->spentTimeDays;
                $totalSpentTimeHours += $task->spentTimeHours;
            }
        }

        if ($totalSpentTimeHours >= self::HOURS_IN_DAY) {
            $totalSpentTimeDays += floor($totalSpentTimeHours / self::HOURS_IN_DAY);
            $totalSpentTimeHours = $totalSpentTimeHours % self::HOURS_IN_DAY;
        }

        return new GetTasksTotalTimeResponse(['days' => $totalSpentTimeDays, 'hours' => $totalSpentTimeHours]);
    }
}