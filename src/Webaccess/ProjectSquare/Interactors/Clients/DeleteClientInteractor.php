<?php

namespace Webaccess\ProjectSquare\Interactors\Clients;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Interactors\Projects\DeleteProjectInteractor;
use Webaccess\ProjectSquare\Interactors\Projects\GetProjectsInteractor;
use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Requests\Projects\DeleteProjectRequest;
use Webaccess\ProjectSquare\Responses\Clients\DeleteClientResponse;

class DeleteClientInteractor
{
    public function __construct(ClientRepository $clientRepository, ProjectRepository $projectRepository)
    {
        $this->repository = $clientRepository;
        $this->projectRepository = $projectRepository;
    }

    public function execute($request)
    {
        $client = $this->getClient($request->clientID);
        $this->deleteProjectsByClientID($request->clientID);
        $this->deleteClient($client);

        return new DeleteClientResponse([
            'client' => $client,
        ]);
    }

    private function getClient($clientID)
    {
        if (!$client = $this->repository->getClient($clientID)) {
            throw new \Exception(Context::get('translator')->translate('clients.client_not_found'));
        }

        return $client;
    }

    private function deleteClient($client)
    {
        $this->repository->deleteClient($client->id);
    }

    private function deleteProjectsByClientID($clientID)
    {
        $projects = (new GetProjectsInteractor($this->projectRepository))->getProjectsByClientID($clientID);
        if (is_array($projects) && sizeof($projects) > 0) {
            foreach ($projects as $project) {
                (new DeleteProjectInteractor($this->projectRepository))->execute(new DeleteProjectRequest([
                    'projectID' => $project->id
                ]));
            }
        }
    }
}