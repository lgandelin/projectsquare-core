<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Step;
use Webaccess\ProjectSquare\Repositories\StepRepository;

class InMemoryStepRepository implements StepRepository
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

    public function getStep($eventID)
    {
        if (isset($this->objects[$eventID])) {
            return $this->objects[$eventID];
        }

        return false;
    }

    public function getSteps($projectID)
    {
        $result = [];
        foreach ($this->objects as $event) {
            if ($event->projectID == $projectID) {
                $result[]= $event;
            }
        }

        return $result;
    }

    public function persistStep(Step $event)
    {
        if (!isset($event->id)) {
            $event->id = self::getNextID();
        }
        $this->objects[$event->id]= $event;

        return $event;
    }

    public function removeStep($eventID)
    {
        if (isset($this->objects[$eventID])) {
            unset($this->objects[$eventID]);
        }
    }
}