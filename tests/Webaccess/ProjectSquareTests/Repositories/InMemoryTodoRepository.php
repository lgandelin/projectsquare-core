<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Todo;
use Webaccess\ProjectSquare\Repositories\TodoRepository;

class InMemoryTodoRepository implements TodoRepository
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

    public function getTodo($eventID)
    {
        if (isset($this->objects[$eventID])) {
            return $this->objects[$eventID];
        }

        return false;
    }

    public function getTodos($projectID)
    {
        $result = [];
        foreach ($this->objects as $event) {
            if ($event->projectID == $projectID) {
                $result[]= $event;
            }
        }

        return $result;
    }

    public function persistTodo(Todo $event)
    {
        if (!isset($event->id)) {
            $event->id = self::getNextID();
        }
        $this->objects[$event->id]= $event;

        return $event;
    }

    public function removeTodo($eventID)
    {
        if (isset($this->objects[$eventID])) {
            unset($this->objects[$eventID]);
        }
    }
}