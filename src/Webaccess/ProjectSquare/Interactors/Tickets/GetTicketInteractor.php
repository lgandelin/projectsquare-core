<?php

namespace Webaccess\ProjectSquare\Interactors\Tickets;

use Webaccess\ProjectSquare\Context;
use Webaccess\ProjectSquare\Repositories\TicketRepository;

class GetTicketInteractor
{
    protected $repository;

    public function __construct(TicketRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getTicket($ticketID, $userID = null)
    {
        if (!$ticket = $this->repository->getTicket($ticketID)) {
            throw new \Exception(Context::get('translator')->translate('tickets.ticket_not_found'));
        }

        if ($userID != null && !$this->repository->isUserAllowedToSeeTicket($userID, $ticket)) {
            throw new \Exception(Context::get('translator')->translate('tickets.user_not_authorized_error'));
        }

        return $ticket;
    }

    public function getTicketWithStates($ticketID, $userID = null)
    {
        if (!$ticket = $this->repository->getTicketWithStates($ticketID)) {
            throw new \Exception(Context::get('translator')->translate('tickets.ticket_not_found'));
        }

        if ($userID != null && !$this->repository->isUserAllowedToSeeTicket($userID, $ticket)) {
            throw new \Exception(Context::get('translator')->translate('tickets.user_not_authorized_error'));
        }

        return $ticket;
    }

    public function getTicketsList($userID, $projectID = null, $allocatedUserID = null, $statusID = null, $typeID = null)
    {
        return $this->repository->getTicketsList($userID, $projectID, $allocatedUserID, $statusID, $typeID);
    }

    public function getTicketsPaginatedList($userID, $limit, $projectID = null, $allocatedUserID = null, $statusID = null, $typeID = null)
    {
        return $this->repository->getTicketsPaginatedList($userID, $limit, $projectID, $allocatedUserID, $statusID, $typeID);
    }

    public function getTicketStatesPaginatedList($ticketID, $limit)
    {
        return $this->repository->getTicketStatesPaginatedList($ticketID, $limit);
    }
}
