<?php

namespace Webaccess\ProjectSquareTests\Repositories;

use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;

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

    public function getProject($projectID)
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

    public function persistProject(Project $project)
    {
        if (!isset($project->id)) {
            $project->id = self::getNextID();
        }
        $project->users = [];

        $this->objects[$project->id]= $project;

        return $project;
    }

    public function getProjects()
    {
        // TODO: Implement getProjects() method.
    }

    public function getUserProjects($userID)
    {
        // TODO: Implement getUserProjects() method.
    }

    public function getProjectWithUsers($projectID)
    {
        // TODO: Implement getProjectWithUsers() method.
    }

    public function createProject($name, $clientID, $websiteFrontURL, $websiteBackURL, $refererID, $status)
    {
        // TODO: Implement createProject() method.
    }

    public function addUserToProject($project, $user, $roleID)
    {
        $project->users[]= $user->id;
    }

    public function isUserInProject($project, $userID)
    {
        if (isset($project->users)) {
            return in_array($userID, $project->users);
        }

        return false;
    }

    public function removeUserFromProject($project, $userID)
    {
        // TODO: Implement removeUserFromProject() method.
    }
}