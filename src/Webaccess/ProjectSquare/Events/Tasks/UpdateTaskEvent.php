<?php

namespace Webaccess\ProjectSquare\Events\Tasks;

use Symfony\Component\EventDispatcher\Event;

class UpdateTaskEvent extends Event
{
    public $taskID;
    public $requesterUserID;

    public function __construct($taskID, $requesterUserID)
    {
        $this->taskID = $taskID;
        $this->requesterUserID = $requesterUserID;
    }
}
