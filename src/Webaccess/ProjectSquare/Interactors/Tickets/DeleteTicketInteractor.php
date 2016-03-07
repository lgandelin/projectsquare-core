<?php

namespace Webaccess\ProjectSquare\Interactors\Tickets;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Events\Tickets\DeleteTicketEvent;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Requests\Tickets\DeleteTicketRequest;
use Webaccess\ProjectSquare\Responses\Tickets\DeleteTicketResponse;

class DeleteTicketInteractor extends GetTicketInteractor
{
    protected $repository;
    protected $projectRepository;

    public function __construct(TicketRepository $repository, ProjectRepository $projectRepository)
    {
        parent::__construct($repository);
        $this->projectRepository = $projectRepository;
    }

    public function execute(DeleteTicketRequest $request)
    {
        $ticket = $this->getTicket($request->ticketID);
        $this->validate($request, $ticket);
        $this->dispatchEvent($ticket);
        $this->deleteTicket($ticket);

        return new DeleteTicketResponse([
            'ticket' => $ticket,
        ]);
    }

    private function validate(DeleteTicketRequest $request, Ticket $ticket)
    {
        $this->validateRequesterPermissions($request, $ticket);
    }

    private function validateRequesterPermissions(DeleteTicketRequest $request, Ticket $ticket)
    {
        if (!$this->isUserAuthorizedToDeleteTicket($request->requesterUserID, $ticket)) {
            throw new \Exception(Context::get('translator')->translate('users.ticket_deletion_not_allowed'));
        }
    }

    private function isUserAuthorizedToDeleteTicket($userID, Ticket $ticket)
    {
        $project = $this->projectRepository->getProject($ticket->projectID);

        return $this->projectRepository->isUserInProject($project, $userID);
    }

    private function deleteTicket(Ticket $ticket)
    {
        $this->repository->deleteTicket($ticket->id);
    }

    private function dispatchEvent(Ticket $ticket)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::DELETE_TICKET,
            new DeleteTicketEvent($ticket)
        );
    }
}
