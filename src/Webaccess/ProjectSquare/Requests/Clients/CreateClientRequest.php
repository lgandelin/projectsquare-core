<?php

namespace Webaccess\ProjectSquare\Requests\Clients;

use Webaccess\ProjectSquare\Requests\Request;

class CreateClientRequest extends Request
{
    public $name;
    public $address;
}