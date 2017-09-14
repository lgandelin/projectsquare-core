<?php

namespace Webaccess\ProjectSquare\Events\Tickets;

use Symfony\Component\EventDispatcher\Event;

class UpdateTicketEvent extends Event
{
    public $ticketID;
    public $requesterUserID;

    public function __construct($ticketID, $requesterUserID)
    {
        $this->ticketID = $ticketID;
        $this->requesterUserID = $requesterUserID;
    }
}
