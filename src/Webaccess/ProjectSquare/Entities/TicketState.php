<?php

namespace Webaccess\ProjectSquare\Entities;

class TicketState
{
    public $id;
    public $ticketID;
    public $authorUserID;
    public $allocatedUserID;
    public $statusID;
    public $priority;
    public $dueDate;
    public $estimatedTimeDays;
    public $estimatedTimeHours;
    public $spentTimeDays;
    public $spentTimeHours;
    public $comments;
}
