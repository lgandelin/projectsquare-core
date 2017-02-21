<?php
namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Phase;

interface PhaseRepository
{
    public function getPhase($phaseID);

    public function getPhases($projectID);

    public function persistPhase(Phase $phase);

    public function removePhase($phaseID);
}