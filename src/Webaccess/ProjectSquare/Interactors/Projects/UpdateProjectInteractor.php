<?php

namespace Webaccess\ProjectSquare\Interactors\Projects;

use Mockery;
use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Projects\UpdateProjectEvent;
use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Projects\UpdateProjectRequest;
use Webaccess\ProjectSquare\Responses\Projects\UpdateProjectResponse;

class UpdateProjectInteractor extends GetProjectInteractor
{
    protected $repository;

    public function __construct(ProjectRepository $repository, UserRepository $userRepository, ClientRepository $clientRepository)
    {
        parent::__construct($repository);
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(UpdateProjectRequest $request)
    {
        $project = $this->getProject($request->projectID);
        $this->validateRequest($request);
        $this->updateProject($project, $request);
        $this->dispatchEvent($project->id);

        return new UpdateProjectResponse([
            'project' => $project
        ]);
    }

    private function updateProject(Project $project, UpdateProjectRequest $request)
    {
        if ($request->name) {
            $project->name = $request->name;
        }

        if ($request->color) {
            $project->color = $request->color;
        }

        if ($request->clientID) {
            $project->clientID = $request->clientID;
        }

        if ($request->websiteFrontURL) {
            $project->websiteFrontURL = $request->websiteFrontURL;
        }

        if ($request->websiteBackURL) {
            $project->websiteBackURL = $request->websiteBackURL;
        }

        if ($request->tasksScheduledTime) {
            $project->tasksScheduledTime = $request->tasksScheduledTime;
        }

        if ($request->ticketsScheduledTime) {
            $project->ticketsScheduledTime = $request->ticketsScheduledTime;
        }

        $this->repository->persistProject($project);
    }

    private function validateRequest(UpdateProjectRequest $request)
    {
        $this->validateRequesterPermissions($request);
        if ($request->clientID != null) {
            $this->validateClient($request->clientID);
        }
    }

    private function validateRequesterPermissions(UpdateProjectRequest $request)
    {
        if ($user = $this->userRepository->getUser($request->requesterUserID)) {
            if (!$user->isAdministrator) {
                throw new \Exception(Context::get('translator')->translate('projects.user_not_authorized'));
            }
        }

        return false;
    }

    private function validateClient($clientID)
    {
        if (!$client = $this->clientRepository->getClient($clientID)) {
            throw new \Exception(Context::get('translator')->translate('clients.client_not_found'));
        }
    }

    private function dispatchEvent($projectID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_PROJECT,
            new UpdateProjectEvent($projectID)
        );
    }
}