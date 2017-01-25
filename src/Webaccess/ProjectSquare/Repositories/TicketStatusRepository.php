<?php

namespace Webaccess\ProjectSquare\Repositories;

interface TicketStatusRepository
{
    public static function getTicketStatus($ticketStatusID);

    public static function getTicketStatuses();

    public static function getTicketStatusesPaginatedList($limit);

    public static function createTicketStatus($name, $include_in_planning);

    public static function updateTicketStatus($ticketStatusID, $name, $include_in_planning);

    public static function deleteTicketStatus($ticketStatusID);
}
