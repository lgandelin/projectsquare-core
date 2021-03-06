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
        if (isset($this->objects[$taskID])) {
            return $this->objects[$taskID];
        }

        return false;
    }

    public function getTasks($userID, $projectID = null, $statusID = null, $allocatedUserID = null, $phaseID = null, $entities = false)
    {
        $result = [];
        foreach ($this->objects as $task) {
            $include = true;

            if ($projectID && $task->projectID != $projectID) {
                $include = false;
            }

            if ($statusID && $task->statusID != $statusID) {
                $include = false;
            }

            if ($allocatedUserID && $task->allocatedUserID != $allocatedUserID) {
                $include = false;
            }

            if ($include) {
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

    public function deleteTask($taskID)
    {
        if (isset($this->objects[$taskID])) {
            unset($this->objects[$taskID]);
        }
    }

    public function deleteTasksByPhaseID($phaseID)
    {
        foreach ($this->objects as $task) {
            if ($task->phaseID == $phaseID) {
                unset($this->objects[$task->id]);
            }
        }
    }

    public function getTasksPaginatedList($userID, $limit, $projectID = null, $statusID = null, $phaseID = null, $allocatedUserID = null, $sortColumn = null, $sortOrder = null)
    {
        // TODO: Implement getTasksPaginatedList() method.
    }

    public function getTasksByProjectID($projectID)
    {
        $result = [];
        foreach ($this->objects as $task) {
            if ($task->projectID == $projectID) {
                $result[]= $task;
            }
        }

        return $result;
    }

    public function getTasksByPhaseID($phaseID)
    {
        $result = [];
        foreach ($this->objects as $task) {
            if ($task->phaseID == $phaseID) {
                $result[]= $task;
            }
        }

        return $result;
    }
}