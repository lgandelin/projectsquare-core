<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Event;
use Webaccess\ProjectSquare\Repositories\EventRepository;

class InMemoryEventRepository implements EventRepository
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

    public function getEvent($eventID)
    {
        if (isset($this->objects[$eventID])) {
            return $this->objects[$eventID];
        }

        return false;
    }

    public function getEvents()
    {
        // TODO: Implement getEvents() method.
    }

    public function getEventsByUser($userID)
    {
        $result = [];
        foreach ($this->objects as $event) {
            if ($event->userID == $userID) {
                $result[]= $event;
            }
        }

        return $result;
    }

    public function persistEvent(Event $event)
    {
        if (!isset($event->id)) {
            $event->id = self::getNextID();
        }
        $this->objects[$event->id]= $event;

        return $event;
    }

    public function removeEvent($eventID)
    {
        if (isset($this->objects[$eventID])) {
            unset($this->objects[$eventID]);
        }
    }
}