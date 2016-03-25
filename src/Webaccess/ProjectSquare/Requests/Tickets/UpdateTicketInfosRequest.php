<?php

namespace Webaccess\ProjectSquare\Requests\Tickets;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateTicketInfosRequest extends Request
{
    public $ticketID;
    public $title;
    public $projectID;
    public $typeID;
    public $description;
    public $requesterUserID;
}
