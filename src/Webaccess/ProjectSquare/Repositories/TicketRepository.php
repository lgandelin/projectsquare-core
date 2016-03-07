<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Ticket;
use Webaccess\ProjectSquare\Entities\TicketState;

interface TicketRepository
{
    public function getTicket($ticketID, $userID = null);

    public function getTicketWithStates($ticketID);

    public function getTicketStatesPaginatedList($ticket, $limit);

    public function getTicketsPaginatedList($userID, $limit, $projectID = null, $allocatedUserID = null, $statusID = null, $typeID = null);

    public function addState($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments);

    public function deleteTicket($ticketID);

    public function isUserAllowedToSeeTicket($userID, $ticket);

    public function persistTicket(Ticket $ticket);

    public function persistTicketState(TicketState $ticketState);
}
