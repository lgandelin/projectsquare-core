<?php

namespace Webaccess\ProjectSquare\Entities;

class Phase
{
    public $id;
    public $name;
    public $projectID;
    public $order;
    public $dueDate;
    public $estimatedDuration;
    public $tasks;

    public function __construct()
    {
        $this->estimatedDuration = 0;
        $this->tasks = [];
    }
}