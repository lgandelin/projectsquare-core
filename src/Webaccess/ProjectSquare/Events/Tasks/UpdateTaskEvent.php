<?php

namespace Webaccess\ProjectSquare\Events\Tasks;

use Symfony\Component\EventDispatcher\Event;

class UpdateTaskEvent extends Event
{
    public $taskID;
    public $oldAllocatedUserID;
    public $requesterUserID;

    public function __construct($taskID, $requesterUserID, $oldAllocatedUserID)
    {
        $this->taskID = $taskID;
        $this->oldAllocatedUserID = $oldAllocatedUserID;
        $this->requesterUserID = $requesterUserID;
    }
}
