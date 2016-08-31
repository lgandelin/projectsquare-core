<?php

namespace Webaccess\ProjectSquare\Interactors\Projects;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Project;
use Webaccess\ProjectSquare\Interactors\Tickets\DeleteTicketInteractor;
use Webaccess\ProjectSquare\Interactors\Tickets\GetTicketsInteractor;
use Webaccess\ProjectSquare\Repositories\EventRepository;
use Webaccess\ProjectSquare\Repositories\NotificationRepository;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Requests\Projects\DeleteProjectRequest;
use Webaccess\ProjectSquare\Requests\Tickets\DeleteTicketRequest;
use Webaccess\ProjectSquare\Responses\Projects\DeleteProjectResponse;

class DeleteProjectInteractor
{
    public function __construct(ProjectRepository $projectRepository, TicketRepository $ticketRepository, EventRepository $eventRepository, NotificationRepository $notificationRepository)
    {
        $this->repository = $projectRepository;
        $this->ticketRepository = $ticketRepository;
        $this->eventRepository = $eventRepository;
        $this->notificationRepository = $notificationRepository;
    }

    public function execute(DeleteProjectRequest $request)
    {
        $project = $this->getProject($request->projectID);
        $this->validateRequest($request, $project);
        $this->deleteLinkedTickets($request);
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

    private function deleteLinkedTickets(DeleteProjectRequest $request)
    {
        $tickets = (new GetTicketsInteractor($this->ticketRepository))->getTicketsByProjectID($request->projectID);

        if (is_array($tickets) && sizeof($tickets)) {
            foreach ($tickets as $ticket) {
                (new DeleteTicketInteractor($this->ticketRepository, $this->repository, $this->eventRepository, $this->notificationRepository))->execute(new DeleteTicketRequest([
                    'ticketID' => $ticket->id,
                    'requesterUserID' => $request->requesterUserID
                ]));
            }
        }
    }

    private function validateRequest($request, $project)
    {
        $this->validateRequesterPermissions($request, $project);
    }

    private function validateRequesterPermissions(DeleteProjectRequest $request, Project $project)
    {
        if (!$this->isUserAuthorizedToDeleteProject($request->requesterUserID, $project)) {
            throw new \Exception(Context::get('translator')->translate('users.project_deletion_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteProject($userID, Project $project)
    {
        return $this->repository->isUserInProject($project->id, $userID);
    }
}