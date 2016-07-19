<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Repositories\TaskRepository;

class InMemoryTaskRepository implements TaskRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
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
}