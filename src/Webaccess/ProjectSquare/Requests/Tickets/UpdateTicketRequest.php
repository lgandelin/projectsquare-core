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
    public $estimatedTimeDays;
    public $estimatedTimeHours;
    public $spentTimeDays;
    public $spentTimeHours;
    public $comments;
    public $requesterUserID;
}
