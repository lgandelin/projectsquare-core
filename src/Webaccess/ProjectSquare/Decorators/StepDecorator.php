<?php

namespace Webaccess\ProjectSquare\Decorators;

use Webaccess\ProjectSquare\Entities\Step;

class StepDecorator
{
    public function decorate(Step $step)
    {
        $step->start_time = $step->startTime->format(DATE_ISO8601);
        $step->end_time = $step->endTime->format(DATE_ISO8601);
        $step->project_id = $step->projectID;

        return $step;
    }
}