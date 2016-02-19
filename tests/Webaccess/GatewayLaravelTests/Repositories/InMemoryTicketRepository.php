<?php

namespace Webaccess\GatewayLaravelTests\Repositories;

use Webaccess\GatewayLaravel\Repositories\TicketRepository;

class InMemoryTicketRepository implements TicketRepository
{
    public static function getTicket($ticketID, $userID = null)
    {
        // TODO: Implement getTicket() method.
    }

    public static function getTicketWithStates($ticketID)
    {
        // TODO: Implement getTicketWithStates() method.
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
        // TODO: Implement createTicket() method.
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

}