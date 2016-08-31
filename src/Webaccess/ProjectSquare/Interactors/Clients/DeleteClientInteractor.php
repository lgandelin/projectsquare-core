<?php

namespace Webaccess\ProjectSquare\Interactors\Clients;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Client;
use Webaccess\ProjectSquare\Entities\Notification;
use Webaccess\ProjectSquare\Interactors\Projects\DeleteProjectInteractor;
use Webaccess\ProjectSquare\Interactors\Projects\GetProjectsInteractor;
use Webaccess\ProjectSquare\Repositories\ClientRepository;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Repositories\UserRepository;
use Webaccess\ProjectSquare\Requests\Clients\DeleteClientRequest;
use Webaccess\ProjectSquare\Requests\Projects\DeleteProjectRequest;
use Webaccess\ProjectSquare\Responses\Clients\DeleteClientResponse;

class DeleteClientInteractor
{
    public function __construct(ClientRepository $clientRepository, ProjectRepository $projectRepository, UserRepository $userRepository, TicketRepository $ticketRepository, EventRepository $eventRepository, NotificationRepository $notificationRepository)
    {
        $this->repository = $clientRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->ticketRepository = $ticketRepository;
        $this->eventRepository = $eventRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(DeleteClientRequest $request)
    {
        $client = $this->getClient($request->clientID);
        $this->validateRequest($request);
        $this->deleteProjectsByClientID($request->clientID, $request->requesterUserID);
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

    private function deleteProjectsByClientID($clientID, $requesterUserID)
    {
        $projects = (new GetProjectsInteractor($this->projectRepository))->getProjectsByClientID($clientID);
        if (is_array($projects) && sizeof($projects) > 0) {
            foreach ($projects as $project) {
                (new DeleteProjectInteractor($this->projectRepository, $this->ticketRepository, $this->userRepository, $this->eventRepository, $this->notificationRepository))->execute(new DeleteProjectRequest([
                    'projectID' => $project->id,
                    'requesterUserID' => $requesterUserID
                ]));
            }
        }
    }

    private function validateRequest($request)
    {
        $this->validateRequesterPermissions($request);
    }

    private function validateRequesterPermissions(DeleteClientRequest $request)
    {
        if (!$this->isUserAuthorizedToDeleteClient($request->requesterUserID)) {
            throw new \Exception(Context::get('translator')->translate('users.client_deletion_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteClient($userID)
    {
        $user = $this->userRepository->getUser($userID);

        return $user->isAdministrator;
    }
}