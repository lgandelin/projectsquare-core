<?php

namespace Webaccess\ProjectSquare\Decorators;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Event;

class EventDecorator
{
    public function decorate(Event $event)
    {
        $event->start_time = $event->startTime->format(DATE_ISO8601);
        $event->end_time = $event->endTime->format(DATE_ISO8601);
        $event->project_id = $event->projectID;

        if (isset($event->projectID)) {
            $project = Context::get('GetProjectInteractor')->getProject($event->projectID);
            if ($event->projectID == $project->id) {
                if (isset($project->color)) {
                    $event->color = $project->color;
                }
                $event->project_client = $project->clientName;
                $event->project_name = $project->name;
            }
        }

        return $event;
    }
}
