<?php

namespace Webaccess\ProjectSquare\Requests\Todos;

use Webaccess\ProjectSquare\Requests\Request;

class CreateTodoRequest extends Request
{
    public $name;
    public $status;
    public $userID;
}
