<?php

namespace Webaccess\ProjectSquare\Events\Tasks;

use Symfony\Component\EventDispatcher\Event;

class UpdateTaskEvent extends Event
{
    public $taskID;

    public function __construct($taskID)
    {
        $this->taskID = $taskID;
    }
}
