<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Task;

interface TaskRepository
{
    public function getTask($taskID);

    public function getTasksPaginatedList($userID, $limit, $projectID = null, $statusID = null, $allocatedUserID = null, $phaseID = false, $sortColumn = null, $sortOrder = null);

    public function persistTask(Task $task);

    public function deleteTask($taskID);

    public function deleteTasksByPhaseID($phaseID);

    public function getTasksByProjectID($projectID);

    public function getTasksByPhaseID($phaseID);
}