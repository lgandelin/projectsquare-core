<?php

namespace Webaccess\ProjectSquare\Requests\Tickets;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteTicketRequest extends Request
{
    public $ticketID;
    public $requesterUserID;
}
