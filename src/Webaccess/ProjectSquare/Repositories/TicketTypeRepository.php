<?php

namespace Webaccess\ProjectSquare\Repositories;

interface TicketTypeRepository
{
    public static function getTicketType($ticketTypeID);

    public static function getTicketTypes();

    public static function getTicketTypesPaginatedList($limit);

    public static function createTicketType($name);

    public static function updateTicketType($ticketTypeID, $name);

    public static function deleteTicketType($ticketTypeID);
}
