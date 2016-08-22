<?php

namespace Webaccess\ProjectSquare\Repositories;

use Webaccess\ProjectSquare\Entities\Todo;

interface TodoRepository
{
    public function getTodo($eventID);

    public function getTodos($projectID);

    public function persistTodo(Todo $todo);

    public function removeTodo($eventID);
}
