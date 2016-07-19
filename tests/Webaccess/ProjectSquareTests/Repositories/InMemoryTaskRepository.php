<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Task;
use Webaccess\ProjectSquare\Repositories\TaskRepository;

class InMemoryTaskRepository implements TaskRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    public function getNextID()
    {
        return count($this->objects) + 1;
    }

    public function getTask($taskID)
    {
        return $this->objects[$taskID];
    }

    public function getTasks($projectID = null)
    {
        $result = [];
        foreach ($this->objects as $task) {
            if (!$projectID || $projectID && $task->projectID == $projectID) {
                $result[]= $task;
            }
        }

        return $result;
    }

    public function persistTask(Task $task)
    {
        if (!isset($task->id)) {
            $task->id = self::getNextID();
        }
        $this->objects[$task->id]= $task;

        return $task;
    }
}