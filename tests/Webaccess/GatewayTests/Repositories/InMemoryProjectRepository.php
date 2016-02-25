<?php

namespace Webaccess\GatewayTests\Repositories;

use Webaccess\Gateway\Entities\Project;
use Webaccess\Gateway\Repositories\ProjectRepository;

class InMemoryProjectRepository implements ProjectRepository
{
    public static $objects;

    public function __construct()
    {
        self::$objects = [];
    }

    public function getNextID()
    {
        return count(self::$objects) + 1;
    }

    public function getProject($projectID, $userID = null)
    {
        if (isset(self::$objects[$projectID])) {
            return self::$objects[$projectID];
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

        self::$objects[$project->id]= $project;

        return $project->id;
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

    public function addUserToProject($project, $userID, $roleID)
    {
        // TODO: Implement addUserToProject() method.
    }

    public function isUserInProject($project, $userID)
    {
        // TODO: Implement isUserInProject() method.
    }

    public function removeUserFromProject($project, $userID)
    {
        // TODO: Implement removeUserFromProject() method.
    }
}