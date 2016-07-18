<?php

namespace Webaccess\ProjectSquare\Events\Todos;

use Symfony\Component\EventDispatcher\Event;
use Webaccess\ProjectSquare\Entities\Todo;

class CreateTodoEvent extends Event
{
    public $todo;

    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }
}
