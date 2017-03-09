<?php

namespace Webaccess\ProjectSquare\Interactors\Projects;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Projects\CreateProjectEvent;
use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Projects\CreateProjectRequest;
use Webaccess\ProjectSquare\Responses\Projects\CreateProjectResponse;

class CreateProjectInteractor
{
    protected $repository;

    public function __construct(ProjectRepository $repository, UserRepository $userRepository, ClientRepository $clientRepository)
    {
        $this->repository = $repository;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(CreateProjectRequest $request)
    {
        $this->validateRequest($request);
        $project = $this->createProject($request);
        $this->dispatchEvent($project->id);

        return new CreateProjectResponse([
            'project' => $project
        ]);
    }

    private function createProject(CreateProjectRequest $request)
    {
        $project = new Project();
        $project->name = $request->name;
        $project->color = $request->color;
        $project->clientID = $request->clientID;
        $project->statusID = $request->statusID;

        return $this->repository->persistProject($project);
    }

    private function validateRequest(CreateProjectRequest $request)
    {
        $this->validRequesterPermissions($request);
        if ($request->clientID != null) {
            $this->validateClient($request->clientID);
        }
    }

    private function validRequesterPermissions(CreateProjectRequest $request)
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
            Events::CREATE_PROJECT,
            new CreateProjectEvent($projectID)
        );
    }
}