<?php

namespace Webaccess\GatewayTests\Repositories;

use Webaccess\Gateway\Entities\Ticket;
use Webaccess\Gateway\Entities\TicketState;
use Webaccess\Gateway\Repositories\TicketRepository;

class InMemoryTicketRepository implements TicketRepository
{
    public $objects;
    public $ticketStateRepository;

    public function __construct()
    {
        $this->objects = [];
        $this->ticketStateRepository = new InMemoryTicketStateRepository();
    }

    public function getNextID()
    {
        return count($this->objects) + 1;
    }

    public function getTicket($ticketID, $userID = null)
    {
        return $this->objects[$ticketID];
    }

    public function getTicketWithStates($ticketID)
    {
        $ticket = $this->objects[$ticketID];
        $ticket->states = $this->ticketStateRepository->getTicketStates($ticketID);

        return $ticket;
    }

    public function getTicketStatesPaginatedList($ticket, $limit)
    {
        // TODO: Implement getTicketStatesPaginatedList() method.
    }

    public function getTicketsPaginatedList($userID, $limit, $projectID = null, $allocatedUserID = null, $statusID = null, $typeID = null)
    {
        // TODO: Implement getTicketsPaginatedList() method.
    }

    public function createTicket($title, $projectID, $typeID, $description, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        $ticket = new Ticket();
        $ticket->id = self::getNextID();
        $ticket->title = $title;
        $ticket->projectID = $projectID;
        $ticket->typeID = $typeID;
        $ticket->description = $description;
        $this->objects[$ticket->id]= $ticket;

        self::addState($ticket->id, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments);

        return self::getTicket($ticket->id);
    }

    public function updateTicket($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        self::addState($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments);

        return self::getTicket($ticketID);
    }

    public function addState($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        $ticketState = new TicketState();
        $ticketState->id = $this->ticketStateRepository->getNextID();
        $ticketState->ticketID = $ticketID;
        $ticketState->statusID = $statusID;
        $ticketState->authorUserID = $authorUserID;
        $ticketState->allocatedUserID = $allocatedUserID;
        $ticketState->priority = $priority;
        $ticketState->dueDate = $dueDate;
        $ticketState->comments = $comments;
        $this->ticketStateRepository->objects[$ticketState->id]= $ticketState;
    }

    public function deleteTicket($ticketID)
    {
        // TODO: Implement deleteTicket() method.
    }

    public function isUserAllowedToSeeTicket($userID, $ticket)
    {
        // TODO: Implement isUserAllowedToSeeTicket() method.
    }

    public function persistTicket(Ticket $ticket)
    {
        if (!isset($ticket->id)) {
            $ticket->id = self::getNextID();
        }
        $this->objects[$ticket->id]= $ticket;

        return $ticket;
    }

    public function persistTicketState(TicketState $ticketState)
    {
        $ticketState->id = $this->ticketStateRepository->getNextID();
        $this->ticketStateRepository->objects[$ticketState->id]= $ticketState;

        return $ticketState;
    }
}