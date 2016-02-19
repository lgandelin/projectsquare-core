<?php

namespace Webaccess\GatewayLaravelTests\Repositories;

use Webaccess\GatewayLaravel\Entities\Ticket;
use Webaccess\GatewayLaravel\Repositories\TicketRepository;

class InMemoryTicketRepository implements TicketRepository
{
    public static $objects;

    public function __construct()
    {
        self::$objects = [];
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
        self::$objects[]= $ticket;
    }

    public static function updateInfos($ticketID, $title, $projectID, $typeID, $description)
    {
        // TODO: Implement updateInfos() method.
    }

    public static function updateTicket($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        // TODO: Implement updateTicket() method.
    }

    public static function addState($ticketID, $statusID, $authorUserID, $allocatedUserID, $priority, $dueDate, $comments)
    {
        // TODO: Implement addState() method.
    }

    public static function deleteTicket($ticketID)
    {
        // TODO: Implement deleteTicket() method.
    }

    public static function isUserAllowedToSeeTicket($userID, $ticket)
    {
        // TODO: Implement isUserAllowedToSeeTicket() method.
    }

    private static function getNextID()
    {
        return count(self::$objects);
    }

}