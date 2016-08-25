<?php

namespace Webaccess\ProjectSquare\Interactors\Projects;

use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Requests\Projects\CreateProjectRequest;

class CreateProjectInteractor
{
    protected $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CreateProjectRequest $request)
    {
        return $this->createProject($request);
    }

    private function createProject(CreateProjectRequest $request)
    {
        $project = new Project();
        $project->clientID = $request->clientID;
        $project->color = $request->color;
        $project->tasksScheduledTime = $request->tasksScheduledTime;
        $project->ticketsScheduledTime = $request->ticketsScheduledTime;
        $project->status = $request->status;
        $project->websiteFrontURL = $request->websiteFrontURL;
        $project->websiteBackURL = $request->websiteBackURL;
        $project->slackChannel = $request->slackChannel;

        return $this->repository->persistProject($project);
    }
}