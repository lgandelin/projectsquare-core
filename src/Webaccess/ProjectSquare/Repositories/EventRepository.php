<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Event;

interface EventRepository
{
    public function getEvent($eventID);

    public function getEvents($userID, $projectID, $ticketID, $taskID);

    public function persistEvent(Event $event);

    public function removeEvent($eventID);
}
