<?php

namespace Webaccess\ProjectSquare\Requests\Tasks;

use Webaccess\ProjectSquare\Requests\Request;

class CreateTaskRequest extends Request
{
    public $name;
    public $status;
    public $userID;
}
