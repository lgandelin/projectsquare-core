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

    public function getTask($eventID)
    {
        if (isset($this->objects[$eventID])) {
            return $this->objects[$eventID];
        }

        return false;
    }

    public function getTasks($projectID)
    {
        $result = [];
        foreach ($this->objects as $event) {
            if ($event->projectID == $projectID) {
                $result[]= $event;
            }
        }

        return $result;
    }

    public function persistTask(Task $event)
    {
        if (!isset($event->id)) {
            $event->id = self::getNextID();
        }
        $this->objects[$event->id]= $event;

        return $event;
    }

    public function removeTask($eventID)
    {
        if (isset($this->objects[$eventID])) {
            unset($this->objects[$eventID]);
        }
    }
}