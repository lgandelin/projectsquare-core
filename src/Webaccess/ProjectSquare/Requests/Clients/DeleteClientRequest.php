<?php

namespace Webaccess\ProjectSquare\Requests\Clients;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteClientRequest extends Request
{
    public $clientID;
    public $requesterUserID;
}