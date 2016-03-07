<?php

namespace Webaccess\ProjectSquare\Events\Tickets;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Ticket;

class DeleteTicketEvent extends Event
{
    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
