<?php

namespace Webaccess\ProjectSquare\Events\Tasks;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Task;

class CreateTaskEvent extends Event
{
    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}