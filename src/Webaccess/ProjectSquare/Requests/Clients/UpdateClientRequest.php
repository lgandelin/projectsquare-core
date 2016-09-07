<?php

namespace Webaccess\ProjectSquare\Requests\Clients;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateClientRequest extends Request
{
    public $clientID;
    public $name;
    public $address;
}