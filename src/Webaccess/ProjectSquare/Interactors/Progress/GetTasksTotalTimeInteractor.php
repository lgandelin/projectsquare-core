<?php

namespace Webaccess\ProjectSquare\Interactors\Progress;

use Webaccess\ProjectSquare\Interactors\Tasks\GetTasksInteractor;
use Webaccess\ProjectSquare\Repositories\TaskRepository;
use Webaccess\ProjectSquare\Requests\Tasks\GetTasksRequest;

class GetTasksTotalTimeInteractor
{
    const HOURS_IN_DAY = 7;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->repository = $taskRepository;
        $this->getTasksInteractor = new GetTasksInteractor($taskRepository);
    }

    public function getTasksTotalEstimatedTime($projectID)
    {
        $tasks = $this->getTasksInteractor->execute(new GetTasksRequest([
            'projectID' => $projectID
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

        return array($totalEstimatedTimeDays, $totalEstimatedTimeHours);
    }
}