<?php

namespace Webaccess\ProjectSquare\Requests\Todos;

use Webaccess\ProjectSquare\Requests\Request;

class DeleteTodoRequest extends Request
{
    public $todoID;
    public $requesterUserID;
}
