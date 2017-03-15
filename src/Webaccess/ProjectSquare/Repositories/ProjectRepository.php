<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Project;

interface ProjectRepository
{
    public function getProject($projectID);

    public function getProjects();

    public function getUserProjects($projectID);

    public function getProjectWithUsers($projectID);

    public function getProjectsPaginatedList($limit);

    public function getProjectsByClientID($clientID);

    public function deleteProject($projectID);

    public function addUserToProject($projectID, $userID, $roleID);

    public function isUserInProject($projectID, $userID);

    public function removeUserFromProject($projectID, $userID);

    public function persistProject(Project $project);

    public function getCurrentProjects($userID);
}
