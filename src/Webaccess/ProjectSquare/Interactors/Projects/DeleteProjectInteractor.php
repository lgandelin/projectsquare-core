<?php

namespace Webaccess\ProjectSquare\Interactors\Projects;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Requests\Projects\DeleteProjectRequest;
use Webaccess\ProjectSquare\Responses\Projects\DeleteProjectResponse;

class DeleteProjectInteractor
{
    public function __construct(ProjectRepository $projectRepository)
    {
        $this->repository = $projectRepository;
    }

    public function execute(DeleteProjectRequest $request)
    {
        $project = $this->getProject($request->projectID);
        //$this->validateRequest($request, $project);
        $this->deleteProject($project);

        return new DeleteProjectResponse([
            'project' => $project,
        ]);
    }

    private function getProject($projectID)
    {
        if (!$project = $this->repository->getProject($projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }

        return $project;
    }

    private function deleteProject($project)
    {
        $this->repository->deleteProject($project->id);
    }
}