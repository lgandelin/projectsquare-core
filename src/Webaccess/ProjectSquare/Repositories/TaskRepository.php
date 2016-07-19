<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Task;

interface TaskRepository
{
    public function getTask($taskID);

    public function getTasks($projectID = null);

    public function persistTask(Task $task);
}