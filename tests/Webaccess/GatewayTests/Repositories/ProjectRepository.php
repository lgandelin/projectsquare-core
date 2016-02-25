<?php

namespace Webaccess\GatewayTests\Repositories;

use Webaccess\Gateway\Repositories\ProjectRepository;
use Webaccess\GatewayLaravel\Models\Project;

class InMemoryProjectRepository implements ProjectRepository
{
    public $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    public function getNextID()
    {
        return count($this->objects) + 1;
    }

    public function getProject($projectID, $userID = null)
    {
        if (isset($this->objects[$projectID])) {
            return $this->objects[$projectID];
        }

        return false;
    }

    public function getProjectsPaginatedList($limit)
    {
        // TODO: Implement getProjectsPaginatedList() method.
    }

    public function updateProject($projectID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        return self::getProject($projectID);
    }

    public function deleteProject($projectID)
    {
        // TODO: Implement deleteProject() method.
    }

    public function isUserAllowedToSeeProject($userID, $project)
    {
        // TODO: Implement isUserAllowedToSeeProject() method.
    }

    public function persistProject(Project $project)
    {
        if (!isset($project->id)) {
            $project->id = self::getNextID();
        }
        $this->objects[$project->id]= $project;

        return $project->id;
    }
}