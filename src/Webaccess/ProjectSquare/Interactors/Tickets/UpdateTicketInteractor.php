<?php

namespace Webaccess\ProjectSquare\Interactors\Tickets;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\TicketState;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tickets\UpdateTicketEvent;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Requests\Tickets\UpdateTicketRequest;
use Webaccess\ProjectSquare\Responses\Tickets\UpdateTicketResponse;

class UpdateTicketInteractor extends GetTicketInteractor
{
    protected $repository;
    protected $projectRepository;

    public function __construct(TicketRepository $repository, ProjectRepository $projectRepository)
    {
        parent::__construct($repository);
        $this->projectRepository = $projectRepository;
    }

    public function execute(UpdateTicketRequest $request)
    {
        $ticket = $this->getTicket($request->ticketID);
        $this->validateRequest($request);
        $ticketState = $this->createTicketState($request);
        $this->dispatchEvent($ticket->id);

        return new UpdateTicketResponse([
            'ticket' => $ticket,
            'ticketState' => $ticketState,
        ]);
    }

    private function validateRequest(UpdateTicketRequest $request)
    {
        $this->validateAuthorUser($request);
        $this->validateAllocatedUser($request);
        $this->validateStatus($request);
        $this->validateDueDate($request);
        $this->validateRequesterPermissions($request);
    }

    private function validateDueDate(UpdateTicketRequest $request)
    {
        if ($request->dueDate && $request->dueDate < new \DateTime('now')) {
            throw new \Exception(Context::get('translator')->translate('tickets.due_date_already_passed'));
        }
    }

    private function validateAuthorUser(UpdateTicketRequest $request)
    {
        //TODO
    }

    private function validateAllocatedUser(UpdateTicketRequest $request)
    {
        //TODO
    }

    private function validateStatus(UpdateTicketRequest $request)
    {
        //TODO
    }

    private function validateRequesterPermissions(UpdateTicketRequest $request)
    {
        if (!$this->isUserAuthorizedToUpdateTicket($request)) {
            throw new \Exception(Context::get('translator')->translate('users.ticket_update_not_allowed'));
        }
    }

    private function isUserAuthorizedToUpdateTicket(UpdateTicketRequest $request)
    {
        $ticket = $this->repository->getTicket($request->ticketID);
        $project = $this->projectRepository->getProject($ticket->projectID);

        return $this->projectRepository->isUserInProject($project, $request->requesterUserID);
    }

    private function createTicketState(UpdateTicketRequest $request)
    {
        $ticketState = new TicketState();
        $ticketState->ticketID = $request->ticketID;
        $ticketState->statusID = $request->statusID;
        $ticketState->authorUserID = $request->authorUserID;
        $ticketState->allocatedUserID = $request->allocatedUserID;
        $ticketState->priority = $request->priority;
        $ticketState->dueDate = $request->dueDate;
        $ticketState->comments = $request->comments;
        $ticketState = $this->repository->persistTicketState($ticketState);

        return $ticketState;
    }

    private function dispatchEvent($ticketID)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_TICKET,
            new UpdateTicketEvent($ticketID)
        );
    }
}
