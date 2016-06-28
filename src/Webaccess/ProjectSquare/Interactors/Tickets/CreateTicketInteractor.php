<?php

namespace Webaccess\ProjectSquare\Interactors\Tickets;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Entities\TicketState;
use Webaccess\ProjectSquare\Events\Tickets\CreateTicketEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Requests\Tickets\CreateTicketRequest;
use Webaccess\ProjectSquare\Responses\Tickets\CreateTicketResponse;

class CreateTicketInteractor
{
    protected $repository;
    protected $projectRepository;

    public function __construct(TicketRepository $repository, ProjectRepository $projectRepository)
    {
        $this->repository = $repository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(CreateTicketRequest $request)
    {
        $this->validateRequest($request);
        $ticket = $this->createTicket($request);
        $ticketState = $this->createTicketState($request, $ticket->id);
        $this->dispatchEvent($ticket->id);

        return new CreateTicketResponse([
            'ticket' => $ticket,
            'ticketState' => $ticketState,
        ]);
    }

    private function validateRequest(CreateTicketRequest $request)
    {
        $this->validateProject($request);
        $this->validateTitle($request);
        $this->validateAllocatedUser($request);
        $this->validateRequesterPermissions($request);
    }

    private function validateTitle(CreateTicketRequest $request)
    {
        if (!$request->title) {
            throw new \Exception(Context::get('translator')->translate('tickets.title_required'));
        }
    }

    private function validateProject(CreateTicketRequest $request)
    {
        if (!$project = $this->projectRepository->getProject($request->projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.project_not_found'));
        }
    }

    private function validateAllocatedUser(CreateTicketRequest $request)
    {
        if ($request->allocatedUserID && !$this->isUserInProject($request->projectID, $request->allocatedUserID)) {
            throw new \Exception(Context::get('translator')->translate('users.allocated_user_not_in_project'));
        }
    }

    private function validateRequesterPermissions(CreateTicketRequest $request)
    {
        if (!$this->isUserAuthorizedToCreateTicket($request)) {
            throw new \Exception(Context::get('translator')->translate('users.ticket_creation_not_allowed'));
        }
    }

    private function isUserAuthorizedToCreateTicket(CreateTicketRequest $request)
    {
        $project = $this->projectRepository->getProject($request->projectID);

        return $this->projectRepository->isUserInProject($project, $request->requesterUserID);
    }

    private function isUserInProject($projectID, $userID)
    {
        $project = $this->projectRepository->getProject($projectID);

        return $this->projectRepository->isUserInProject($project, $userID);
    }

    private function createTicket(CreateTicketRequest $request)
    {
        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->projectID = $request->projectID;
        $ticket->typeID = $request->typeID;
        $ticket->description = $request->description;

        return $this->repository->persistTicket($ticket);
    }

    private function createTicketState(CreateTicketRequest $request, $ticketID)
    {
        $ticketState = new TicketState();
        $ticketState->ticketID = $ticketID;
        $ticketState->statusID = $request->statusID;
        $ticketState->authorUserID = ($request->authorUserID) ? $request->authorUserID : $request->requesterUserID;
        $ticketState->allocatedUserID = $request->allocatedUserID;
        $ticketState->priority = $request->priority;
        $ticketState->dueDate = $request->dueDate;
        $ticketState->estimatedTime = $request->estimatedTime;
        $ticketState->comments = $request->comments;

        return $this->repository->persistTicketState($ticketState);
    }

    private function dispatchEvent($ticketID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::CREATE_TICKET,
            new CreateTicketEvent($ticketID)
        );
    }
}
