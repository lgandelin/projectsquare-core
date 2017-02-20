<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Phase;
use Webaccess\ProjectSquare\Repositories\PhaseRepository;

class InMemoryPhaseRepository implements PhaseRepository
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

    public function getPhase($phaseID)
    {
        if (isset($this->objects[$phaseID])) {
            return $this->objects[$phaseID];
        }

        return false;
    }

    public function getPhases($projectID, $entities = false)
    {
        $result = [];
        foreach ($this->objects as $phase) {
            $include = true;

            if ($projectID && $phase->projectID != $projectID) {
                $include = false;
            }

            if ($include) {
                $result[]= $phase;
            }
        }

        return $result;
    }

    public function persistPhase(Phase $phase)
    {
        if (!isset($phase->id)) {
            $phase->id = self::getNextID();
        }
        $this->objects[$phase->id]= $phase;

        return $phase;
    }

    public function deletePhase($phaseID)
    {
        if (isset($this->objects[$phaseID])) {
            unset($this->objects[$phaseID]);
        }
    }
}