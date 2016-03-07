<?php

namespace Webaccess\ProjectSquare\Events\Tickets;

use Symfony\Component\EventDispatcher\Event;

class UpdateTicketEvent extends Event
{
    public $ticketID;

    public function __construct($ticketID)
    {
        $this->ticketID = $ticketID;
    }
}
