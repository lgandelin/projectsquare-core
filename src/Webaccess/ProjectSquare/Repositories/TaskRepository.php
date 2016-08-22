<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Task;

interface TaskRepository
{
    public function getTask($taskID);

    public function getTasks($projectID = null, $statusID = null, $allocatedUserID = null);

    public function getTasksPaginatedList($limit, $projectID = null, $statusID = null, $allocatedUserID = null);

    public function persistTask(Task $task);

    public function deleteTask($taskID);
}