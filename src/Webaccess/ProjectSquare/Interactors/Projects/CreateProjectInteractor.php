<?php

namespace Webaccess\ProjectSquare\Interactors\Projects;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Requests\Projects\CreateProjectRequest;

class CreateProjectInteractor
{
    protected $repository;

    public function __construct(ProjectRepository $repository, ClientRepository $clientRepository)
    {
        $this->repository = $repository;
        $this->clientRepository = $clientRepository;
    }

    public function execute(CreateProjectRequest $request)
    {
        return $this->createProject($request);
    }

    private function createProject(CreateProjectRequest $request)
    {
        $project = new Project();
        $project->color = $request->color;
        $project->tasksScheduledTime = $request->tasksScheduledTime;
        $project->ticketsScheduledTime = $request->ticketsScheduledTime;
        $project->websiteFrontURL = $request->websiteFrontURL;
        $project->websiteBackURL = $request->websiteBackURL;
        $project->slackChannel = $request->slackChannel;

        if ($request->clientID) {
            $this->validateClient($request->clientID);
            $project->clientID = $request->clientID;
        }

        return $this->repository->persistProject($project);
    }

    private function validateClient($clientID)
    {
        if (!$client = $this->clientRepository->getClient($clientID)) {
            throw new \Exception(Context::get('translator')->translate('clients.client_not_found'));
        }
    }
}