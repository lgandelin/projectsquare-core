<?php

namespace Webaccess\ProjectSquare\Requests\Todos;

use Webaccess\ProjectSquare\Requests\Request;

class UpdateTodoRequest extends Request
{
    public $todoID;
    public $name;
    public $status;
    public $requesterUserID;
}
