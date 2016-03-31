<?php

namespace Webaccess\ProjectSquare\Requests\Tickets;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateTicketRequest extends Request
{
    public $ticketID;
    public $statusID;
    public $authorUserID;
    public $allocatedUserID;
    public $priority;
    public $dueDate;
    public $estimatedTime;
    public $comments;
    public $requesterUserID;
}
