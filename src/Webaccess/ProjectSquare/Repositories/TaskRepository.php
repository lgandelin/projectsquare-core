<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Task;

interface TaskRepository
{
    public function getTask($eventID);

    public function getTasks($projectID);

    public function persistTask(Task $event);

    public function removeTask($eventID);
}
