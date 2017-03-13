<?php

namespace Webaccess\ProjectSquare\Interactors\Projects;

use Webaccess\ProjectSquare\Repositories\ProjectRepository;

class GetProjectsInteractor
{
    protected $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProjects($projectID)
    {
        return $this->repository->getUserProjects($projectID);
    }

    public function getCurrentProjects($userID)
    {
        return $this->repository->getCurrentProjects($userID);
    }

    public function getProjectsByClientID($clientID)
    {
        return $this->repository->getProjectsByClientID($clientID);
    }
}
