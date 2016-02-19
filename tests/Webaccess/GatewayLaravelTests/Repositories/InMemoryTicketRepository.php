<?php

namespace Webaccess\GatewayLaravelTests\Repositories;

use Webaccess\GatewayLaravel\Entities\Ticket;
use Webaccess\GatewayLaravel\Entities\TicketState;
use Webaccess\GatewayLaravel\Repositories\TicketRepository;

class InMemoryTicketRepository implements TicketRepository
{
    public static $objects;
    public static $ticketStateRepository;

    public function __construct()
    {
        self::$objects = [];
        self::$ticketStateRepository = new InMemoryTicketStateRepository();
    }

    public static function getNextID()
    {
        return count(self::$objects) + 1;
    }

    public static function getTicket($ticketID, $userID = null)
    {
        return self::$objects[$ticketID];
    }

    public static function getTicketWithStates($ticketID)
    {
        return self::$objects[$ticketID];
    }

    public static function getTicketStatesPaginatedList($ticket, $limit)
    {
        // TODO: Implement getTicketStatesPaginatedList() method.
    }

    public static function getTicketsPaginatedList($userID, $limit, $projectID = null, $allocatedUserID = null, $statusID = null, $typeID = null)
    {
        // TODO: Implement getTicketsPaginatedList() method.
    }

    public static function createTicket($title, $projectID, $typeID, $description, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        $ticket = new Ticket();
        $ticket->id = self::getNextID();
        $ticket->title = $title;
        $ticket->projectID = $projectID;
        $ticket->typeID = $typeID;
        $ticket->description = $description;

        $ticketState = new TicketState();
        $ticketState->id = self::$ticketStateRepository->getNextID();
        $ticketState->statusID = $statusID;
        $ticketState->authorUserID = $authorUserID;
        $ticketState->allocatedUserID = $allocatedUserID;
        $ticketState->priority = $priority;
        $ticketState->dueDate = $dueDate;
        $ticketState->comments = $comments;
        self::$ticketStateRepository->objects[$ticketState->id]= $ticketState;

        $ticket->addState($ticketState);
        self::$objects[$ticket->id]= $ticket;
    }

    public static function updateInfos($ticketID, $title, $projectID, $typeID, $description)
    {
        // TODO: Implement updateInfos() method.
    }

    public static function updateTicket($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        self::addState($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments);

        return self::getTicket($ticketID);
    }

    public static function addState($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        $ticket = self::getTicket($ticketID);

        $ticketState = new TicketState();
        $ticketState->id = self::$ticketStateRepository->getNextID();
        $ticketState->statusID = $statusID;
        $ticketState->authorUserID = $authorUserID;
        $ticketState->allocatedUserID = $allocatedUserID;
        $ticketState->priority = $priority;
        $ticketState->dueDate = $dueDate;
        $ticketState->comments = $comments;
        self::$ticketStateRepository->objects[$ticketState->id]= $ticketState;

        $ticket->addState($ticketState);
    }

    public static function deleteTicket($ticketID)
    {
        // TODO: Implement deleteTicket() method.
    }

    public static function isUserAllowedToSeeTicket($userID, $ticket)
    {
        // TODO: Implement isUserAllowedToSeeTicket() method.
    }
}