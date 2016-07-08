<?php

namespace Webaccess\ProjectSquare\Interactors\Tickets;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Events\Events;
use Webaccess\ProjectSquare\Events\Tickets\UpdateTicketInfosEvent;
use Webaccess\ProjectSquare\Repositories\ProjectRepository;
use Webaccess\ProjectSquare\Repositories\TicketRepository;
use Webaccess\ProjectSquare\Requests\Tickets\UpdateTicketInfosRequest;
use Webaccess\ProjectSquare\Responses\Tickets\UpdateTicketInfosResponse;

class UpdateTicketInfosInteractor extends GetTicketInteractor
{
    protected $repository;
    protected $projectRepository;

    public function __construct(TicketRepository $repository, ProjectRepository $projectRepository)
    {
        parent::__construct($repository);
        $this->repository = $repository;
        $this->projectRepository = $projectRepository;
    }

    public function execute(UpdateTicketInfosRequest $request)
    {
        $ticket = $this->getTicket($request->ticketID);
        $this->validateRequest($request);
        $this->updateTicketInfos($ticket, $request);
        $this->dispatchEvent($ticket);

        return new UpdateTicketInfosResponse([
            'ticket' => $ticket,
        ]);
    }

    private function validateRequest(UpdateTicketInfosRequest $request)
    {
        $this->validateProject($request);
        $this->validateTitle($request);
        $this->validateRequesterPermissions($request);
    }

    private function validateProject(UpdateTicketInfosRequest $request)
    {
        if ($request->projectID && !$project = $this->projectRepository->getProject($request->projectID)) {
            throw new \Exception(Context::get('translator')->translate('projects.not_found'));
        }
    }

    private function validateTitle(UpdateTicketInfosRequest $request)
    {
        if (!$request->title) {
            throw new \Exception(Context::get('translator')->translate('tickets.title_required'));
        }
    }

    private function validateRequesterPermissions(UpdateTicketInfosRequest $request)
    {
        if (!$this->isUserAuthorizedToUpdateTicket($request)) {
            throw new \Exception(Context::get('translator')->translate('users.ticket_update_not_allowed'));
        }
    }

    private function isUserAuthorizedToUpdateTicket(UpdateTicketInfosRequest $request)
    {
        $ticket = $this->repository->getTicket($request->ticketID);

        return $this->projectRepository->isUserInProject($ticket->projectID, $request->requesterUserID);
    }

    private function updateTicketInfos(Ticket $ticket, UpdateTicketInfosRequest $request)
    {
        $ticket->title = $request->title;
        $ticket->projectID = $request->projectID;
        $ticket->typeID = $request->typeID;
        $ticket->description = $request->description;
        $this->repository->persistTicket($ticket);
    }

    private function dispatchEvent(Ticket $ticket)
    {
        Context::get('event_dispatcher')->dispatch(
            Events::UPDATE_TICKET_INFOS,
            new UpdateTicketInfosEvent($ticket)
        );
    }
}
