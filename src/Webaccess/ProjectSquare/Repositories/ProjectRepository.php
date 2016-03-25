<?php

namespace Webaccess\ProjectSquare\Repositories;

interface ProjectRepository
{
    public function getProject($projectID);

    public function getProjects();

    public function getUserProjects($projectID);

    public function getProjectWithUsers($projectID);

    public function getProjectsPaginatedList($limit);

    public function createProject($name, $clientID, $websiteFrontURL, $websiteBackURL, $refererID, $status, $color);

    public function updateProject($projectID, $name, $clientID, $websiteFrontURL, $websiteBackURL, $refererID, $status, $color);

    public function deleteProject($projectID);

    public function addUserToProject($project, $userID, $roleID);

    public function isUserInProject($project, $userID);

    public function removeUserFromProject($project, $userID);
}