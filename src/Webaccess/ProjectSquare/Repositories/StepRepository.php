<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Step;

interface StepRepository
{
    public function getStep($eventID);

    public function getSteps($projectID);

    public function persistStep(Step $event);

    public function removeStep($eventID);
}
